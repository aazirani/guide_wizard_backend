<?php

namespace UserFrosting\Sprinkle\GuideWizard\ServicesProvider;

use Birke\Rememberme\Authenticator as RememberMe;
use Illuminate\Database\Capsule\Manager as Capsule;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserFrosting\Sprinkle\Account\Authenticate\Authenticator;
use UserFrosting\Sprinkle\Account\Authenticate\AuthGuard;
use UserFrosting\Sprinkle\Account\Authenticate\Hasher;
use UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager;
use UserFrosting\Sprinkle\Account\Database\Models\User;
use UserFrosting\Sprinkle\Account\Log\UserActivityDatabaseHandler;
use UserFrosting\Sprinkle\Account\Log\UserActivityProcessor;
use UserFrosting\Sprinkle\Account\Repository\PasswordResetRepository;
use UserFrosting\Sprinkle\Account\Repository\VerificationRepository;
use UserFrosting\Sprinkle\Account\Twig\AccountExtension;
use UserFrosting\Sprinkle\Core\Facades\Debug;
use UserFrosting\Sprinkle\Core\Log\MixedFormatter;

/**
 * Registers services for the sprinkle.
 *
 */
class ServicesProvider
{
    /**
     * Register services for the sprinkle.
     *
     * @param Container $container A DI container implementing ArrayAccess and container-interop.
     */
    public function register($container)
    {
        /**
         * Extend the 'classMapper' service to add parts model classes.
         *
         * Mappings added: User, Group, Role, Permission, Activity, PasswordReset, Verification
         */
        $container->extend('classMapper', function ($classMapper, $c) {
            $classMapper->setClassMapping('answer', 'UserFrosting\Sprinkle\GuideWizard\Database\Models\Answer');
            $classMapper->setClassMapping('answer_sprunje', 'UserFrosting\Sprinkle\GuideWizard\Sprunje\AnswerSprunje');

            $classMapper->setClassMapping('language', 'UserFrosting\Sprinkle\GuideWizard\Database\Models\Language');
            $classMapper->setClassMapping('language_sprunje', 'UserFrosting\Sprinkle\GuideWizard\Sprunje\LanguageSprunje');

            $classMapper->setClassMapping('logic', 'UserFrosting\Sprinkle\GuideWizard\Database\Models\Logic');
            $classMapper->setClassMapping('logic_sprunje', 'UserFrosting\Sprinkle\GuideWizard\Sprunje\LogicSprunje');

            $classMapper->setClassMapping('question', 'UserFrosting\Sprinkle\GuideWizard\Database\Models\Question');
            $classMapper->setClassMapping('question_sprunje', 'UserFrosting\Sprinkle\GuideWizard\Sprunje\QuestionSprunje');

            $classMapper->setClassMapping('step', 'UserFrosting\Sprinkle\GuideWizard\Database\Models\Step');
            $classMapper->setClassMapping('step_sprunje', 'UserFrosting\Sprinkle\GuideWizard\Sprunje\StepSprunje');
            $classMapper->setClassMapping('apps_step_sprunje', 'UserFrosting\Sprinkle\GuideWizard\Sprunje\AppsStepSprunje');

            $classMapper->setClassMapping('subTask', 'UserFrosting\Sprinkle\GuideWizard\Database\Models\SubTask');
            $classMapper->setClassMapping('subTask_sprunje', 'UserFrosting\Sprinkle\GuideWizard\Sprunje\SubTaskSprunje');

            $classMapper->setClassMapping('task', 'UserFrosting\Sprinkle\GuideWizard\Database\Models\Task');
            $classMapper->setClassMapping('task_sprunje', 'UserFrosting\Sprinkle\GuideWizard\Sprunje\TaskSprunje');

            $classMapper->setClassMapping('text', 'UserFrosting\Sprinkle\GuideWizard\Database\Models\Text');
            $classMapper->setClassMapping('text_sprunje', 'UserFrosting\Sprinkle\GuideWizard\Sprunje\TextSprunje');

            $classMapper->setClassMapping('translation', 'UserFrosting\Sprinkle\GuideWizard\Database\Models\Translation');
            $classMapper->setClassMapping('translation_sprunje', 'UserFrosting\Sprinkle\GuideWizard\Sprunje\TranslationSprunje');

            $classMapper->setClassMapping('textSetting', 'UserFrosting\Sprinkle\GuideWizard\Database\Models\TextSetting');
            $classMapper->setClassMapping('textSetting_sprunje', 'UserFrosting\Sprinkle\GuideWizard\Sprunje\TextSettingSprunje');

            return $classMapper;
        });
    }
}
