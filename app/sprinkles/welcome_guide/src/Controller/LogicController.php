<?php
namespace UserFrosting\Sprinkle\WelcomeGuide\Controller;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\NotFoundException as NotFoundException;
use UserFrosting\Fortress\RequestDataTransformer;
use UserFrosting\Fortress\RequestSchema;
use UserFrosting\Fortress\ServerSideValidator;
use UserFrosting\Sprinkle\WelcomeGuide\Controller\UtilityClasses\TranslationsUtilities;
use UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Answer;
use UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Language;
use UserFrosting\Sprinkle\WelcomeGuide\Database\Models\SubTask;
use UserFrosting\Support\Exception\BadRequestException;
use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Support\Exception\HttpException;
use UserFrosting\Fortress\Adapter\JqueryValidationAdapter;
use UserFrosting\Sprinkle\FormGenerator\Form;
use UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Text;

class LogicController extends SimpleController
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
        if (!$authorizer->checkAccess($currentUser, 'view_logics'))
        {
            throw new ForbiddenException();
        }

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;
        $sprunje = $classMapper->createInstance('logic_sprunje', $classMapper, $params);
        $sprunje->extendQuery(function ($query)
        {
            return $query->with('creator');
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
        if (!$authorizer->checkAccess($currentUser, 'view_logics'))
        {
            throw new ForbiddenException();
        }

        return $this
            ->ci
            ->view
            ->render($response, 'pages/logics.html.twig');
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
        if (!$authorizer->checkAccess($currentUser, 'create_logic'))
        {
            throw new ForbiddenException();
        }

        // Load validator rules
        $schema = new RequestSchema('schema://forms/addLogic.json');
        $validator = new JqueryValidationAdapter($schema, $this
            ->ci
            ->translator);

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;

        // Generate the form
        $form = new Form($schema);

        LogicController::setFormValues($form, $classMapper, null);

        // Using custom form here to add the javascript we need fo Typeahead.
        $this
            ->ci
            ->view
            ->render($response, 'FormGenerator/modal-large.html.twig', ['box_id' => $get['box_id'], 'box_title' => 'LOGIC.CREATE', 'submit_button' => 'CREATE', 'form_action' => 'api/logics', 'fields' => $form->generate() , 'validators' => $validator->rules('json', true) , ]);
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
        if (!$authorizer->checkAccess($currentUser, 'create_logic'))
        {
            throw new ForbiddenException();
        }

        /** @var UserFrosting\Sprinkle\Core\MessageStream $ms */
        $ms = $this
            ->ci->alerts;

        // Load the request schema
        $schema = new RequestSchema('schema://forms/addLogic.json');

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

        //If the input does not match the required style, stop adding the object.
	    if(!LogicController::isValidExpression($data['expression'])){
            $ms->addMessageTranslated('danger', 'VALIDATE.EXPRESSION', $data);
            $error = true;
        }

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;

        //Add the creator id to the sent data
        $data['creator_id'] = $currentUser->id;

        $ids = array();
        preg_match_all('/\d+/', $data['expression'], $answerIds);
        // Loop through the found ids array
		foreach($answerIds[0] as $key => $value){
            //remove the { and } signs and add to array
			array_push($ids, $value);
        }

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
        Capsule::transaction(function () use ($classMapper, $data, $ms, $config, $currentUser, $ids)
        {

            // Create the object
            $logic = $classMapper->createInstance('logic', $data);
            // Store new logic to database
            $logic->save();

            if(count($ids) > 0){
                //add the sent tag ids to this logic
				$logic->answers()->attach($ids);
            }

            if($data['subTasks']){
                $logic->subTasks()->sync(array_map('intval', explode(",", $data['subTasks'])));
            } else {
                $logic->subTasks()->sync(null);
            }


            // Create activity record
            $this
                ->ci
                ->userActivityLogger
                ->info("User {$currentUser->user_name} created a new logic with the name {$logic
                ->name}.", ['type' => 'logic_created', 'user_id' => $currentUser->id]);

            $ms->addMessageTranslated('success', 'LOGIC.CREATED', $data);
        });

        return $response->withStatus(200);
    }

    function isValidExpression($expr) {
        // Replace logical operators and operands with PHP-valid equivalents
    $jsExpr = str_replace(['and', 'or', 'xor', '!'], ['&&', '||', '^', '!'], $expr);
    $jsExpr = preg_replace('/\b\d+\b/', 'true', $jsExpr);

    // Check if parentheses are balanced
    $depth = 0;
    for ($i = 0; $i < strlen($jsExpr); $i++) {
        if ($jsExpr[$i] === '(') {
            $depth++;
        } else if ($jsExpr[$i] === ')') {
            if ($depth === 0) {
                return false;
            }
            $depth--;
        }
    }
    if ($depth !== 0) {
        return false;
    }

    // Check if expression is just "!"
    if(trim($jsExpr) === '!') {
        return false;
    }

    // Try to evaluate the expression
    $result = @eval("return $jsExpr;");
    if ($result === NULL) {
        return false;
    }

    return true;
    }

    protected function getLogicFromParams($params)
    {
        // Load the request schema
        $schema = new RequestSchema('schema://requests/logic-get-by-id.yaml');
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
        $logic = $classMapper->staticMethod('logic', 'where', 'id', $data['logic_id'])->with('creator')
            ->first();

        return $logic;
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
        $logic = $this->getLogicFromParams($args);

        // If the object doesn't exist, return 404
        if (!$logic)
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
        if (!$authorizer->checkAccess($currentUser, 'delete_logic', ['logic' => $logic]))
        {
            throw new ForbiddenException();
        }
        /** @var UserFrosting\Sprinkle\Core\MessageStream $ms */
        $ms = $this
            ->ci->alerts;

        /** @var UserFrosting\Config\Config $config */
        $config = $this
            ->ci->config;
        $name = $logic
            ->name;
        // Begin transaction - DB will be rolled back if an exception occurs
        Capsule::transaction(function () use ($logic, $name, $currentUser)
        {
            $logic->answers()->sync(null);
            $logic->subTasks()->sync(null);

            $logic->delete();
            
            unset($logic);

            // Create activity record
            $this
                ->ci
                ->userActivityLogger
                ->info("User {$currentUser->user_name} deleted the logic with the name {$name}.", ['type' => 'logic_deleted', 'user_id' => $currentUser->id]);
        });

        $ms->addMessageTranslated('success', 'LOGIC.DELETION_SUCCESSFUL', ['name' => $name]);

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
        $logic = $this->getLogicFromParams($args);

        // If the object doesn't exist, return 404
        if (!$logic)
        {
            throw new NotFoundException($request, $response);
        }

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;

        // Get the object to edit
        $logic = $classMapper->staticMethod('logic', 'where', 'id', $logic->id)
            ->first();

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this
            ->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this
            ->ci->currentUser;

        // Access-controlled resource - check that currentUser has permission to edit fields
        if (!$authorizer->checkAccess($currentUser, 'update_logic_field', ['logic' => $logic]))
        {
            throw new ForbiddenException();
        }

        /** @var UserFrosting\Config\Config $config */
        $config = $this
            ->ci->config;

        // Load validation rules
        $schema = new RequestSchema('schema://forms/addLogic.json');
        $validator = new JqueryValidationAdapter($schema, $this
            ->ci
            ->translator);

        // Generate the form
        $form = new Form($schema, $logic);

        LogicController::setFormValues($form, $classMapper, $logic);

        // Render the template / form
        $this
            ->ci
            ->view
            ->render($response, 'FormGenerator/modal-large.html.twig', ['box_id' => $get['box_id'], 'box_title' => 'LOGIC.EDIT', 'submit_button' => 'EDIT', 'form_action' => 'api/logics/' . $args['logic_id'],
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
        $logic = $this->getLogicFromParams($args);

        if (!$logic)
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
        $schema = new RequestSchema('schema://forms/addLogic.json');

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

        //If the input does not match the required style, stop adding the object.
	    if(!LogicController::isValidExpression($data['expression'])){
            $ms->addMessageTranslated('danger', 'VALIDATE.EXPRESSION', $data);
            $error = true;
            return $response->withStatus(400);
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this
            ->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this
            ->ci->currentUser;

        // Access-controlled resource - check that currentUser has permission to edit submitted fields for this object
        if (!$authorizer->checkAccess($currentUser, 'update_logic_field', ['logic' => $logic]))
        {
            throw new ForbiddenException();
        }

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;

        $ids = array();
        preg_match_all('/\d+/', $data['expression'], $answerIds);
        // Loop through the found ids array
		foreach($answerIds[0] as $key => $value){
            //remove the { and } signs and add to array
			array_push($ids, $value);
        }

        // Begin transaction - DB will be rolled back if an exception occurs
        Capsule::transaction(function () use ($data, $logic, $currentUser, $ids)
        {
            // Update the object and generate success messages
            foreach ($data as $name => $value)
            {
                if ($value != $logic->$name && $name != 'subTasks')
                {
                    $logic->$name = $value;
                }
            }

            $logic->save();

            if(count($ids) > 0){
                //add the sent tag ids to this logic
				$logic->answers()->attach($ids);
            }
            if($data['subTasks']){
                $logic->subTasks()->sync(array_map('intval', explode(",", $data['subTasks'])));
            } else {
                $logic->subTasks()->sync(null);
            }


            // Create activity record
            $this
                ->ci
                ->userActivityLogger
                ->info("User {$currentUser->user_name} updated basic data for logic with the name {$logic
                ->name}.", ['type' => 'logic_updated', 'user_id' => $currentUser->id]);
        });

        $ms->addMessageTranslated('success', 'LOGIC.DETAILS_UPDATED', ['name' => $logic->name]);
        return $response->withJson([], 200, JSON_PRETTY_PRINT);
    }

    public static function setFormValues($form, $classMapper, $logic){
        $answers = Answer::all();
        $answerSelect = [];
        foreach ($answers as $answer) {
            $answerData = [];
            $answerData += ['title' => TranslationsUtilities::getTranslationTextBasedOnMainLanguage($answer->title, $classMapper)];
            $answerData += ['question' => TranslationsUtilities::getTranslationTextBasedOnMainLanguage($answer->question->title, $classMapper)];

            $answerSelect += [$answer->id => $answerData];

        }
        $form->setInputArgument('expression', 'answers', $answerSelect);

        $subTasks = SubTask::all();
        $subTaskSelect = [];
        foreach ($subTasks as $subTask) {
            $subTaskData = [];
            $subTaskData += ['title' => TranslationsUtilities::getTranslationTextBasedOnMainLanguage($subTask->title, $classMapper)];
            $subTaskData += ['task' => TranslationsUtilities::getTranslationTextBasedOnMainLanguage($subTask->task->text, $classMapper)];

            $subTaskSelect += [$subTask->id => $subTaskData];

        }
        $form->setInputArgument('subTaskOptions', 'subTaskOptionElements', $subTaskSelect);

        if($logic){
            $answers = $logic->answers()->get();
            $expressionAnswers = [];
            foreach ($answers as $answer) {
                $answerData = [];
                $answerData += ['title' => TranslationsUtilities::getTranslationTextBasedOnMainLanguage($answer->title, $classMapper)];
                $answerData += ['question' => TranslationsUtilities::getTranslationTextBasedOnMainLanguage($answer->question->title, $classMapper)];

                $expressionAnswers += [$answer->id => $answerData];
            }
            $expressionElements = [[]];
            $arrayFromExpression = LogicController::tokenizeExpression($logic->expression);
            $index = 1;
            foreach ($arrayFromExpression as $singleElement) {
                if(is_numeric($singleElement)){
                    $expressionElements += [$index => [$singleElement => $expressionAnswers[$singleElement]]];
                } elseif(strlen(trim($singleElement)) > 0) {
                    $expressionElements += [$index => [$singleElement => $singleElement]];
                }
                $index++;
            }
            $form->setInputArgument('expression', 'expressionElements', $expressionElements);


            $subTasksIds = $logic->subTasks()->get()->pluck('id')->toArray();
            $form->setInputArgument('subTasks', 'value', implode(",", $subTasksIds));
        }
    }


    static function tokenizeExpression($expression) {
        // Handle the 'not' operator separately by using a lookahead assertion in the regex pattern
        $pattern = '/\!|\d+|\(|\)|and|or|xor/';
        preg_match_all($pattern, $expression, $matches);
        return $matches[0];
    }

}