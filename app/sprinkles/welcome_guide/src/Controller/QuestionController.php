<?php

namespace UserFrosting\Sprinkle\WelcomeGuide\Controller;

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
use UserFrosting\Sprinkle\WelcomeGuide\Controller\UtilityClasses\TranslationsUtilities;
use UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Step;
use UserFrosting\Support\Exception\BadRequestException;
use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Support\Exception\HttpException;

class QuestionController extends SimpleController
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
        if (!$authorizer->checkAccess($currentUser, 'view_questions')) {
            throw new ForbiddenException();
        }

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;
        $sprunje = $classMapper->createInstance('question_sprunje', $classMapper, $params);
        $sprunje->extendQuery(function ($query) {
            return $query->with('creator')
                ->with('step.name.translations.language')
                ->with('title.translations.language')
                ->with('subTitle.translations.language')
                ->with('infoUrl.translations.language')
                ->with('infoDescription.translations.language');
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
        if (!$authorizer->checkAccess($currentUser, 'view_questions')) {
            throw new ForbiddenException();
        }

        return $this
            ->ci
            ->view
            ->render($response, 'pages/questions.html.twig');
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
        if (!$authorizer->checkAccess($currentUser, 'create_question')) {
            throw new ForbiddenException();
        }

        // Load validator rules
        $schema = new RequestSchema('schema://forms/addQuestion.json');
        $validator = new JqueryValidationAdapter($schema, $this
            ->ci
            ->translator);
        // Generate the form
        $form = new Form($schema);

        $classMapper = $this->ci->classMapper;
        TranslationsUtilities::setFormValues($form, $classMapper, QuestionController::getTranslationsVariables(null));

        $steps = STEP::all();
        $stepSelect = [];
        foreach ($steps as $step) {
            $stepSelect += [$step->id => TranslationsUtilities::getTranslationTextBasedOnMainLanguage($step->name, $classMapper)];
        }

        $form->setInputArgument('step_id', 'options', $stepSelect);

        // Using custom form here to add the javascript we need fo Typeahead.
        $this
            ->ci
            ->view
            ->render($response, 'FormGenerator/modal.html.twig', ['box_id' => $get['box_id'], 'box_title' => 'QUESTION.CREATE', 'submit_button' => 'CREATE', 'form_action' => 'api/questions', 'fields' => $form->generate(), 'validators' => $validator->rules('json', true),]);
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
        if (!$authorizer->checkAccess($currentUser, 'create_question')) {
            throw new ForbiddenException();
        }

        /** @var UserFrosting\Sprinkle\Core\MessageStream $ms */
        $ms = $this
            ->ci->alerts;

        // Load the request schema
        $schema = new RequestSchema('schema://forms/addQuestion.json');

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        $error = false;

        // Validate request data
        $validator = new ServerSideValidator($schema, $this
            ->ci
            ->translator);
        if (!$validator->validate($data)) {
            $ms->addValidationErrors($validator);
            $error = true;
        }

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;

        //Add the creator id to the sent data
        $data['creator_id'] = $currentUser->id;

        if ($error) {
            return $response->withStatus(400);
        }

        /** @var UserFrosting\Config\Config $config */
        $config = $this
            ->ci->config;

        $userActivityLogger = $this->ci->userActivityLogger;

        $data['creator_id'] = $currentUser->id;

        // All checks passed!  log events/activities, create customer
        // Begin transaction - DB will be rolled back if an exception occurs
        Capsule::transaction(function () use ($classMapper, $data, $ms, $config, $currentUser, $params, $userActivityLogger) {

            // Create the object
            $question = $classMapper->createInstance('question', $data);

            $question->step_id = QuestionController::getQuestionsStepId($classMapper);

            // Store new question to database
            $question->save();
            TranslationsUtilities::saveTranslations($question, "Question", $params, $classMapper, $currentUser, $this->getTranslationsVariables($question), $userActivityLogger);

            $text = TranslationsUtilities::getTranslationTextBasedOnMainLanguage($question->title, $classMapper);

            // Create activity record
            $this
                ->ci
                ->userActivityLogger
                ->info("User {$currentUser->user_name} created a new question named {$text}.", ['type' => 'question_created', 'user_id' => $currentUser->id]);

            $ms->addMessageTranslated('success', 'QUESTION.CREATED', $data);
        });

        return $response->withStatus(200);
    }

    protected function getQuestionFromParams($params)
    {
        // Load the request schema
        $schema = new RequestSchema('schema://requests/question-get-by-id.yaml');
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
        $question = $classMapper->staticMethod('question', 'where', 'id', $data['question_id'])->with('creator')
            ->with('step')
            ->first();

        return $question;
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
        $question = $this->getQuestionFromParams($args);

        // If the object doesn't exist, return 404
        if (!$question) {
            throw new NotFoundException($request, $response);
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this
            ->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this
            ->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'delete_question', ['question' => $question])) {
            throw new ForbiddenException();
        }
        /** @var UserFrosting\Sprinkle\Core\MessageStream $ms */
        $ms = $this
            ->ci->alerts;

        /** @var UserFrosting\Config\Config $config */
        $config = $this
            ->ci->config;

        $classMapper = $this->ci->classMapper;

        $userActivityLogger = $this->ci->userActivityLogger;

        $text = TranslationsUtilities::getTranslationTextBasedOnMainLanguage($question->title, $classMapper);

        // Begin transaction - DB will be rolled back if an exception occurs
        Capsule::transaction(function () use ($question, $text, $currentUser, $classMapper, $userActivityLogger) {

            QuestionController::deleteObject($question, $classMapper, $userActivityLogger, $currentUser);

            unset($question);

            // Create activity record
            $this
                ->ci
                ->userActivityLogger
                ->info("User {$currentUser->user_name} deleted the question {$text}.", ['type' => 'question_deleted', 'user_id' => $currentUser->id]);
        });

        $ms->addMessageTranslated('success', 'QUESTION.DELETION_SUCCESSFUL', ['name' => $text]);

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
        $question = $this->getQuestionFromParams($args);

        // If the object doesn't exist, return 404
        if (!$question) {
            throw new NotFoundException($request, $response);
        }

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;

        // Get the object to edit
        $question = $classMapper->staticMethod('question', 'where', 'id', $question->id)
            ->first();

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this
            ->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this
            ->ci->currentUser;

        // Access-controlled resource - check that currentUser has permission to edit fields
        if (!$authorizer->checkAccess($currentUser, 'update_question_field', ['question' => $question])) {
            throw new ForbiddenException();
        }

        /** @var UserFrosting\Config\Config $config */
        $config = $this
            ->ci->config;

        // Load validation rules
        $schema = new RequestSchema('schema://forms/addQuestion.json');
        $validator = new JqueryValidationAdapter($schema, $this
            ->ci
            ->translator);

        // Generate the form
        $form = new Form($schema, $question);

        TranslationsUtilities::setFormValues($form, $classMapper, $this->getTranslationsVariables($question));

        $steps = Step::all();
        $stepSelect = [];
        foreach ($steps as $step) {
            $stepSelect += [$step->id => TranslationsUtilities::getTranslationTextBasedOnMainLanguage($step->name, $classMapper)];
        }
        $form->setInputArgument('step_id', 'options', $stepSelect);

        // Render the template / form
        $this
            ->ci
            ->view
            ->render($response, 'FormGenerator/modal.html.twig', ['box_id' => $get['box_id'], 'box_title' => 'QUESTION.EDIT', 'submit_button' => 'EDIT', 'form_action' => 'api/questions/' . $args['question_id'],
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
        $question = $this->getQuestionFromParams($args);

        if (!$question) {
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
        $schema = new RequestSchema('schema://forms/addQuestion.json');

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
        if (!$authorizer->checkAccess($currentUser, 'update_question_field', ['question' => $question])) {
            throw new ForbiddenException();
        }

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;

        $userActivityLogger = $this->ci->userActivityLogger;

        $text = TranslationsUtilities::getTranslationTextBasedOnMainLanguage($question->title, $classMapper);

        // Begin transaction - DB will be rolled back if an exception occurs
        Capsule::transaction(function () use ($data, $question, $currentUser, $classMapper, $post, $text, $userActivityLogger) {
            // Update the object and generate success messages
            foreach ($data as $name => $value) {
                if ($value != $question->$name) {
                    $question->$name = $value;
                }
            }

            $question->step_id = QuestionController::getQuestionsStepId($classMapper);

            TranslationsUtilities::saveTranslations($question, "Question", $post, $classMapper, $currentUser, $this->getTranslationsVariables($question), $userActivityLogger);

            // Create activity record
            $this
                ->ci
                ->userActivityLogger
                ->info("User {$currentUser->user_name} updated basic data for question $text.", ['type' => 'question_updated', 'user_id' => $currentUser->id]);
        });

        $ms->addMessageTranslated('success', 'QUESTION.DETAILS_UPDATED', ['name' => $text]);
        return $response->withJson([], 200, JSON_PRETTY_PRINT);
    }

    private static function getTranslationsVariables($question)
    {
        $arrayOfObjectWithKeyAsKey = array();
        $arrayOfObjectWithKeyAsKey['title'] = isset($subTask) ? $question->title : null;
        $arrayOfObjectWithKeyAsKey['sub_title'] = isset($subTask) ? $question->sub_title : null;
        $arrayOfObjectWithKeyAsKey['info_url'] = isset($subTask) ? $question->info_url : null;
        $arrayOfObjectWithKeyAsKey['info_description'] = isset($subTask) ? $question->info_description : null;

        return $arrayOfObjectWithKeyAsKey;
    }

    public static function deleteObject($question, $classMapper, $userActivityLogger, $currentUser)
    {
        $answers = $classMapper->staticMethod('answer', 'where', 'question_id', $question->id)->get();
        foreach ($answers as $answer) {
            AnswerController::deleteObject($answer, $classMapper, $userActivityLogger, $currentUser);
        }

        $question->delete();

        TranslationsUtilities::deleteTranslations($question, $classMapper, QuestionController::getTranslationsVariables($question), $userActivityLogger, $currentUser);
    }

    public static function getQuestionsStepId($classMapper){
        return $classMapper->createInstance('step')
                ->where('order', '1')
                ->value('id');
    }

}