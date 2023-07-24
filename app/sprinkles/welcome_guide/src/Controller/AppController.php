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
use UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Logic;
use UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Step;
use UserFrosting\Support\Exception\BadRequestException;
use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Support\Exception\HttpException;
use UserFrosting\Fortress\Adapter\JqueryValidationAdapter;
use UserFrosting\Sprinkle\FormGenerator\Form;
use UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Text;
use UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Question;

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
                ->whereHas('tasks.subTasks', function ($query) use ($subTaskIds) {
                    $query->whereIn('id', $subTaskIds);
                })
                ->orWhereHas('tasks.questions')
                ->with([
                    'tasks.questions.answers',
                    'tasks.subTasks' => function ($query) use ($subTaskIds) {
                        $query->whereIn('id', $subTaskIds);
                    },
                ]);
        });

        //set cache headers in order to stop specially IE to cache the result
        return $sprunje->toResponse($response);
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
                ->with('translations.language');
        });
        //set cache headers in order to stop specially IE to cache the result
        return $sprunje->toResponse($response);
    }

    public function getLastUpdatedAt($request, $response, $args)
    {
        $types = ['UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Step',
            'UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Task',
            'UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Question',
            'UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Answer',
            'UserFrosting\Sprinkle\WelcomeGuide\Database\Models\SubTask',
            'UserFrosting\Sprinkle\WelcomeGuide\Database\Models\logic'];

        $lastUpdatedForContent = null;

        foreach ($types as $type) {
            $model = new $type;
            $updatedAt = $model->max('updated_at');

            if ($updatedAt > $lastUpdatedForContent) {
                $lastUpdatedForContent = $updatedAt;
            }
        }

        $types = ['UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Text',
            'UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Translation',
            'UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Language'];

        $lastUpdatedForTranslations = null;

        foreach ($types as $type) {
            $model = new $type;
            $updatedAt = $model->max('updated_at');

            if ($updatedAt > $lastUpdatedForTranslations) {
                $lastUpdatedForTranslations = $updatedAt;
            }
        }

        $data = [
            'last_updated_at_content' => $lastUpdatedForContent,
            'last_updated_at_technical_names' => $lastUpdatedForTranslations
        ];
        return json_encode($data);
    }

}