<?php

namespace UserFrosting\Sprinkle\GuideWizard\Controller;

use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException as NotFoundException;
use UserFrosting\Fortress\Adapter\JqueryValidationAdapter;
use UserFrosting\Fortress\RequestDataTransformer;
use UserFrosting\Fortress\RequestSchema;
use UserFrosting\Fortress\ServerSideValidator;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Sprinkle\FormGenerator\Form;
use UserFrosting\Sprinkle\GuideWizard\Controller\UtilityClasses\TranslationsUtilities;
use UserFrosting\Support\Exception\BadRequestException;
use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Support\Exception\HttpException;

class TextSettingController extends SimpleController
{

    /**
     * Return the list of all objects.
     */
    public function getList($request, $response, $args)
    {

        // GET parameters
        $params = $request->getQueryParams();

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this
            ->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this
            ->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'view_textSettings')) {
            throw new ForbiddenException();
        }

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;
        $sprunje = $classMapper->createInstance('textSetting_sprunje', $classMapper, $params);
        $sprunje->extendQuery(function ($query) {
            return $query->with('creator')
                ->with('title.translations.language');
        });
        //set cache headers in order to stop specially IE to cache the result
        return $sprunje->toResponse($response);
    }

    /**
     * Renders the object's listing page.
     *
     * This page renders a table of objects.
     * This page requires authentication.
     * Request type: GET
     */
    public function pageList($request, $response, $args)
    {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this
            ->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this
            ->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'view_textSettings')) {
            throw new ForbiddenException();
        }

        return $this
            ->ci
            ->view
            ->render($response, 'pages/textSettings.html.twig');
    }

    protected function getTextSettingFromParams($params)
    {
        // Load the request schema
        $schema = new RequestSchema('schema://requests/textSetting-get-by-id.yaml');
        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);
        // Validate, and throw exception on validation errors.
        $validator = new ServerSideValidator($schema, $this
            ->ci
            ->translator);
        if (!$validator->validate($data)) {
            // TODO: encapsulate the communication of error messages from ServerSideValidator to the BadRequestException
            $e = new BadRequestException();
            foreach ($validator->errors() as $idx => $field) {
                foreach ($field as $eidx => $error) {
                    $e->addUserMessage($error);
                }
            }
            throw $e;
        }

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;
        // Get the object to delete
        $textSetting = $classMapper->staticMethod('textSetting', 'where', 'id', $data['textSetting_id'])->with('creator')
            ->first();

        return $textSetting;
    }

    /**
     * editForm function.
     * Renders the form for editing an existing object.
     *
     * This does NOT render a complete page.  Instead, it renders the HTML for the form.
     * The form is rendered in "modal" (for popup) or "panel" mode, depending on the template used.
     *
     * @param mixed $request
     * @param mixed $response
     * @param mixed $args
     *
     * @return void
     */
    public function editForm($request, $response, $args)
    {
        $get = $request->getQueryParams();
        $textSetting = $this->getTextSettingFromParams($args);

        // If the object doesn't exist, return 404
        if (!$textSetting) {
            throw new NotFoundException($request, $response);
        }

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;

        // Get the object to edit
        $textSetting = $classMapper->staticMethod('textSetting', 'where', 'id', $textSetting->id)
            ->first();

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this
            ->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this
            ->ci->currentUser;

        // Access-controlled resource - check that currentUser has permission to edit fields
        if (!$authorizer->checkAccess($currentUser, 'update_textSetting_field', ['textSetting' => $textSetting])) {
            throw new ForbiddenException();
        }

        /** @var UserFrosting\Config\Config $config */
        $config = $this
            ->ci->config;

        // Load validation rules
        $schema = new RequestSchema('schema://forms/addTextSetting.json');
        $validator = new JqueryValidationAdapter($schema, $this
            ->ci
            ->translator);

        // Generate the form
        $form = new Form($schema, $textSetting);

        TranslationsUtilities::setFormValues($form, $classMapper, $this->getTranslationsVariables($textSetting));

        // Render the template / form
        $this
            ->ci
            ->view
            ->render($response, 'FormGenerator/modal.html.twig', ['box_id' => $get['box_id'], 'box_title' => 'TEXT_SETTING.EDIT', 'submit_button' => 'EDIT', 'form_action' => 'api/textSettings/' . $args['textSetting_id'],
                //'form_method'   => 'PUT', //Send form using PUT instead of "POST"
                'fields' => $form->generate(), 'validators' => $validator->rules('json', true),]);
    }

    /**
     * update function.
     * Processes the request to update an existing object's details.
     *
     * @param mixed $request
     * @param mixed $response
     * @param mixed $args
     *
     * @return void
     */
    public function update($request, $response, $args)
    {

        // Get the object from the URL
        $textSetting = $this->getTextSettingFromParams($args);

        if (!$textSetting) {
            throw new NotFoundException($request, $response);
        }

        /** @var UserFrosting\Config\Config $config */
        $config = $this
            ->ci->config;

        // Get the alert message stream
        $ms = $this
            ->ci->alerts;

        // Request POST data
        $post = $request->getParsedBody();

        // Load the request schema
        $schema = new RequestSchema('schema://forms/addTextSetting.json');

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($post);

        // Validate, and halt on validation errors.
        $validator = new ServerSideValidator($schema, $this
            ->ci
            ->translator);
        if (!$validator->validate($data)) {
            $ms->addValidationErrors($validator);
            return $response->withStatus(400);
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this
            ->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this
            ->ci->currentUser;

        // Access-controlled resource - check that currentUser has permission to edit submitted fields for this object
        if (!$authorizer->checkAccess($currentUser, 'update_textSetting_field', ['textSetting' => $textSetting])) {
            throw new ForbiddenException();
        }

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;

        $userActivityLogger = $this->ci->userActivityLogger;

        $text = TranslationsUtilities::getTranslationTextBasedOnMainLanguage($textSetting->title, $classMapper);

        // Begin transaction - DB will be rolled back if an exception occurs
        Capsule::transaction(function () use ($data, $textSetting, $currentUser, $classMapper, $post, $text, $userActivityLogger) {

            // Update the object and generate success messages
            foreach ($data as $name => $value) {
                if ($value != $textSetting->$name) {
                    $textSetting->$name = $value;
                }
            }

            TranslationsUtilities::saveTranslations($textSetting, "Setting", $post, $classMapper, $currentUser, $this->getTranslationsVariables($textSetting), $userActivityLogger, false);

            // Create activity record
            $this
                ->ci
                ->userActivityLogger
                ->info("User {$currentUser->user_name} updated basic data for Text Setting with the name {$text}.", ['type' => 'textSetting_updated', 'user_id' => $currentUser->id]);
        });

        $ms->addMessageTranslated('success', 'TEXT_SETTING.DETAILS_UPDATED', ['name' => $text]);
        return $response->withJson([], 200, JSON_PRETTY_PRINT);
    }

    private static function getTranslationsVariables($textSetting)
    {
        $arrayOfObjectWithKeyAsKey = array();
        $arrayOfObjectWithKeyAsKey['title'] = isset($textSetting) ? $textSetting->title : null;

        return $arrayOfObjectWithKeyAsKey;
    }

}