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
use UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Logic;
use UserFrosting\Support\Exception\BadRequestException;
use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Support\Exception\HttpException;

class AppController extends SimpleController
{

    /**
     * Return the list of all steps.
     */
    public function getStepList($request, $response, $args)
    {
        // GET parameters
        $params = $request->getQueryParams();

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this
            ->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this
            ->ci->currentUser;

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;

        //get the array of selected answers from route
        preg_match_all('/\d+/', $args['answerIds'], $answerMatchList);
        $inputAnswerIds = $answerMatchList[0];

        //get all the logics
        $logics = Logic::all();

        //define the array containing the final subTasks
        $subTaskIds = array();

        //iterate over all the logics to evaluate each one based on the users answers
        foreach ($logics as $logic) {
            //get the answer_ids and !answer_ids from the expression
            $selectedAnswersIds = array();
            $negatedAnswersIds = array();
            preg_match_all('/\d+/', $logic->expression, $selectedAnswersIds);
            preg_match_all('/!\d+/', $logic->expression, $negatedAnswersIds);

            //replace the {answer_id} with logical 1 if the user has selected it and with 0 if the user has not selected it.
            foreach ($selectedAnswersIds[0] as $key => $value) {
                if (in_array($value, $inputAnswerIds)) {
                    $logic->expression = str_replace($value, "1", $logic->expression);
                } else {
                    $logic->expression = str_replace($value, "0", $logic->expression);
                }
            }
            //replace the {!answer_id} with logical 1 if the user has not selected it and with 0 if the user has selected it.
            foreach ($negatedAnswersIds[0] as $key => $value) {
                if (in_array($value, $inputAnswerIds)) {
                    $logic->expression = str_replace($value, "0", $logic->expression);
                } else {
                    $logic->expression = str_replace($value, "1", $logic->expression);
                }
            }

            //evaluate the expression
            if (eval("if (" . $logic->expression . "){ return 1; } else {return 0;}")) {
                //get the ids to all the subTasks corresponding to this logic inorder to only return subTasks that are selected
                $subTaskIds = array_merge($subTaskIds, $logic->subTasks->pluck('id')->toArray());
            }
        }

        $sprunje = $classMapper->createInstance('apps_step_sprunje', $classMapper, $params);
        $sprunje->extendQuery(function ($query) use ($subTaskIds) {
            return $query
                ->where(function ($q) use ($subTaskIds) {
                    // Condition for steps with at least one question
                    $q->whereHas('questions')
                        // Condition for steps with at least one task with a subTask in $subTaskIds
                        ->orWhereHas('tasks.subTasks', function ($query) use ($subTaskIds) {
                            $query->whereIn('id', $subTaskIds);
                        });
                })
                // Load only those tasks which have a subTask in $subTaskIds
                ->with([
                    'questions.answers',
                    'tasks' => function ($query) use ($subTaskIds) {
                        $query->whereHas('subTasks', function ($subQuery) use ($subTaskIds) {
                            $subQuery->whereIn('id', $subTaskIds)
                                ->orderBy('order', 'asc');
                        });
                    },
                    'tasks.subTasks' => function ($query) use ($subTaskIds) {
                        $query->whereIn('id', $subTaskIds)
                            ->orderBy('order', 'asc');
                    }
                ])->orderBy('order', 'asc');
        });

        //set cache headers in order to stop specially IE to cache the result
        return $sprunje->toResponse($response)
            ->withHeader('Access-Control-Allow-Origin', '*');
    }

    public function getTranslations($request, $response, $args)
    {
        // GET parameters
        $params = $request->getQueryParams();

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this
            ->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this
            ->ci->currentUser;

        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this
            ->ci->classMapper;
        $sprunje = $classMapper->createInstance('text_sprunje', $classMapper, $params);
        $sprunje->extendQuery(function ($query) {
            return $query
                ->whereHas('translations.language', function ($query) {
                    $query->where('is_active', 1);
                })
                ->with(['translations' => function ($query) {
                    $query->whereHas('language', function ($query) {
                        $query->where('is_active', 1);
                    })->with('language');
                }]);
        });
        //set cache headers in order to stop specially IE to cache the result
        return $sprunje->toResponse($response)
            ->withHeader('Access-Control-Allow-Origin', '*');
    }

    public function getLastUpdatedAt($request, $response, $args)
    {
        $classMapper = $this->ci->classMapper;

        $typesForObjects = [
            'logic_created', 'logic_updated', 'logic_deleted',
            'question_created', 'question_updated', 'question_deleted',
            'answer_created', 'answer_updated', 'answer_deleted',
            'language_created', 'language_updated', 'language_deleted',
            'step_created', 'step_updated', 'step_deleted',
            'subTask_created', 'subTask_updated', 'subTask_deleted',
            'task_created', 'task_updated', 'task_deleted'
        ];

        $lastUpdatedForContent = $classMapper->createInstance('activity')
            ->whereIn('type', $typesForObjects)
            ->max('occurred_at');

        $typesForTranslations = [
            'language_created', 'language_updated', 'language_deleted',
            'text_created', 'text_updated', 'text_deleted'
        ];

        $lastUpdatedForTranslations = $classMapper->createInstance('activity')
            ->whereIn('type', $typesForTranslations)
            ->max('occurred_at');

        $data = [
            'last_updated_at_content' => $lastUpdatedForContent,
            'last_updated_at_technical_names' => $lastUpdatedForTranslations
        ];

        // Set the response header to allow all origins
        header("Access-Control-Allow-Origin: *");

        // Set the response content type to JSON
        header("Content-Type: application/json");

        // Encode the data as JSON and return it
        return json_encode($data);
    }

}