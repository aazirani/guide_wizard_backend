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
use UserFrosting\Sprinkle\GuideWizard\Database\Models\Language;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\Text;
use UserFrosting\Support\Exception\BadRequestException;
use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Support\Exception\HttpException;

class TranslationController extends SimpleController
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
        if (!$authorizer->checkAccess($currentUser, 'view_translations'))
        {
            throw new ForbiddenException();
        }

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;
        $sprunje = $classMapper->createInstance('translation_sprunje', $classMapper, $params);
        $sprunje->extendQuery(function ($query)
        {
            return $query->with('creator')
                ->with('text')
                ->with('language');
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
        if (!$authorizer->checkAccess($currentUser, 'view_translations'))
        {
            throw new ForbiddenException();
        }

        return $this
            ->ci
            ->view
            ->render($response, 'pages/translations.html.twig');
    }

    /**
     * createForm function.
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
    public function createForm($request, $response, $args)
    {
        // Get the alert message stream
        $ms = $this
            ->ci->alerts;
        // Request GET data
        $get = $request->getQueryParams();

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this
            ->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this
            ->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'create_translation'))
        {
            throw new ForbiddenException();
        }

        // Load validator rules
        $schema = new RequestSchema('schema://forms/addTranslation.json');
        $validator = new JqueryValidationAdapter($schema, $this
            ->ci
            ->translator);
        // Generate the form
        $form = new Form($schema);

        $texts = TEXT::all();
        $textSelect = [];
        foreach ($texts as $text)
        {
            $textSelect += [$text->id => $text->technical_name];
        }
        $form->setInputArgument('text_id', 'options', $textSelect);
        
        $languages = LANGUAGE::all();
        $languageSelect = [];
        foreach ($languages as $language)
        {
            $languageSelect += [$language->id => $language->language_code];
        }
        $form->setInputArgument('language_id', 'options', $languageSelect);
        
        // Using custom form here to add the javascript we need fo Typeahead.
        $this
            ->ci
            ->view
            ->render($response, 'FormGenerator/modal.html.twig', ['box_id' => $get['box_id'], 'box_title' => 'TRANSLATION.CREATE', 'submit_button' => 'CREATE', 'form_action' => 'api/translations', 'fields' => $form->generate() , 'validators' => $validator->rules('json', true) , ]);
    }

    /**
     * Processes the request to create a new object.
     *
     * Processes the request from the object creation form, checking that:
     * 1. The logged-in user has the necessary permissions to update the posted field(s);
     * 2. The submitted data is valid.
     * This route requires authentication.
     * Request type: POST
     * @see getModalCreate
     */
    public function create($request, $response, $args)
    {
        // Get POST parameters
        $params = $request->getParsedBody();

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this
            ->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this
            ->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'create_translation'))
        {
            throw new ForbiddenException();
        }

        /** @var UserFrosting\Sprinkle\Core\MessageStream $ms */
        $ms = $this
            ->ci->alerts;

        // Load the request schema
        $schema = new RequestSchema('schema://forms/addTranslation.json');

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        $error = false;

        // Validate request data
        $validator = new ServerSideValidator($schema, $this
            ->ci
            ->translator);
        if (!$validator->validate($data))
        {
            $ms->addValidationErrors($validator);
            $error = true;
        }

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;

        //Add the creator id to the sent data
        $data['creator_id'] = $currentUser->id;

        if ($error)
        {
            return $response->withStatus(400);
        }

        /** @var UserFrosting\Config\Config $config */
        $config = $this
            ->ci->config;

        $data['creator_id'] = $currentUser->id;

        // All checks passed!  log events/activities, create customer
        // Begin transaction - DB will be rolled back if an exception occurs
        Capsule::transaction(function () use ($classMapper, $data, $ms, $config, $currentUser)
        {

            // Create the object
            $translation = $classMapper->createInstance('translation', $data);
            // Store new translation to database
            $translation->save();

            // Create activity record
            $this
                ->ci
                ->userActivityLogger
                ->info("User {$currentUser->user_name} created a new translation for the technical name {$translation
                ->text->technical_name}.", ['type' => 'translation_created', 'user_id' => $currentUser->id]);

            $ms->addMessageTranslated('success', 'TRANSLATION.CREATED', $data);
        });

        return $response->withStatus(200);
    }

    protected function getTranslationFromParams($params)
    {
        // Load the request schema
        $schema = new RequestSchema('schema://requests/translation-get-by-id.yaml');
        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);
        // Validate, and throw exception on validation errors.
        $validator = new ServerSideValidator($schema, $this
            ->ci
            ->translator);
        if (!$validator->validate($data))
        {
            // TODO: encapsulate the communication of error messages from ServerSideValidator to the BadRequestException
            $e = new BadRequestException();
            foreach ($validator->errors() as $idx => $field)
            {
                foreach ($field as $eidx => $error)
                {
                    $e->addUserMessage($error);
                }
            }
            throw $e;
        }

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;
        // Get the object to delete
        $translation = $classMapper->staticMethod('translation', 'where', 'id', $data['translation_id'])->with('creator')
            ->first();

        return $translation;
    }

    /**
     * Processes the request to delete an existing object.
     *
     * Before doing so, checks that:
     * 1. You have permission to delete the object.
     * This route requires authentication.
     * Request type: DELETE
     */
    public function delete($request, $response, $args)
    {
        $translation = $this->getTranslationFromParams($args);

        // If the object doesn't exist, return 404
        if (!$translation)
        {
            throw new NotFoundException($request, $response);
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this
            ->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this
            ->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'delete_translation', ['translation' => $translation]))
        {
            throw new ForbiddenException();
        }
        /** @var UserFrosting\Sprinkle\Core\MessageStream $ms */
        $ms = $this
            ->ci->alerts;

        /** @var UserFrosting\Config\Config $config */
        $config = $this
            ->ci->config;
        $text = $translation
            ->text->technical_name;
        // Begin transaction - DB will be rolled back if an exception occurs
        Capsule::transaction(function () use ($translation, $text, $currentUser)
        {
            $translation->delete();
            unset($translation);

            // Create activity record
            $this
                ->ci
                ->userActivityLogger
                ->info("User {$currentUser->user_name} deleted the translation for the technical name {$text}.", ['type' => 'translation_deleted', 'user_id' => $currentUser->id]);
        });

        $ms->addMessageTranslated('success', 'TRANSLATION.DELETION_SUCCESSFUL', ['name' => $text]);

        //return $response->withStatus(200);
        return $response->withJson([], 200, JSON_PRETTY_PRINT);
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
        $translation = $this->getTranslationFromParams($args);

        // If the object doesn't exist, return 404
        if (!$translation)
        {
            throw new NotFoundException($request, $response);
        }

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;

        // Get the object to edit
        $translation = $classMapper->staticMethod('translation', 'where', 'id', $translation->id)
            ->first();

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this
            ->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this
            ->ci->currentUser;

        // Access-controlled resource - check that currentUser has permission to edit fields
        if (!$authorizer->checkAccess($currentUser, 'update_translation_field', ['translation' => $translation]))
        {
            throw new ForbiddenException();
        }

        /** @var UserFrosting\Config\Config $config */
        $config = $this
            ->ci->config;

        // Load validation rules
        $schema = new RequestSchema('schema://forms/addTranslation.json');
        $validator = new JqueryValidationAdapter($schema, $this
            ->ci
            ->translator);

        // Generate the form
        $form = new Form($schema, $translation);

        $texts = TEXT::all();
        $textSelect = [];
        foreach ($texts as $text)
        {
            $textSelect += [$text->id => $text->technical_name];
        }
        $form->setInputArgument('text_id', 'options', $textSelect);
        
        $languages = LANGUAGE::all();
        $languageSelect = [];
        foreach ($languages as $language)
        {
            $languageSelect += [$language->id => $language->language_code];
        }
        $form->setInputArgument('language_id', 'options', $languageSelect);

        // Render the template / form
        $this
            ->ci
            ->view
            ->render($response, 'FormGenerator/modal.html.twig', ['box_id' => $get['box_id'], 'box_title' => 'TRANSLATION.EDIT', 'submit_button' => 'EDIT', 'form_action' => 'api/translations/' . $args['translation_id'],
        //'form_method'   => 'PUT', //Send form using PUT instead of "POST"
        'fields' => $form->generate() , 'validators' => $validator->rules('json', true) , ]);
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
        $translation = $this->getTranslationFromParams($args);

        if (!$translation)
        {
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
        $schema = new RequestSchema('schema://forms/addTranslation.json');

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($post);

        // Validate, and halt on validation errors.
        $validator = new ServerSideValidator($schema, $this
            ->ci
            ->translator);
        if (!$validator->validate($data))
        {
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
        if (!$authorizer->checkAccess($currentUser, 'update_translation_field', ['translation' => $translation]))
        {
            throw new ForbiddenException();
        }

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;

        // Begin transaction - DB will be rolled back if an exception occurs
        Capsule::transaction(function () use ($data, $translation, $currentUser)
        {
            // Update the object and generate success messages
            foreach ($data as $name => $value)
            {
                if ($value != $translation->$name)
                {
                    $translation->$name = $value;
                }
            }

            $translation->save();

            // Create activity record
            $this
                ->ci
                ->userActivityLogger
                ->info("User {$currentUser->user_name} updated basic data for translation for the technical name {$translation
                ->text->technical_name}.", ['type' => 'translation_updated', 'user_id' => $currentUser->id]);
        });

        $ms->addMessageTranslated('success', 'TRANSLATION.DETAILS_UPDATED', ['name' => $translation->text->technical_name]);
        return $response->withJson([], 200, JSON_PRETTY_PRINT);
    }
}