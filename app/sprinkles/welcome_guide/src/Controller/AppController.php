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
        $sprunje = $classMapper->createInstance('apps_step_sprunje', $classMapper, $params);
        $sprunje->extendQuery(function ($query) {
            return $query
                ->with('tasks.questions.answers')
                ->with('tasks.subTasks');
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