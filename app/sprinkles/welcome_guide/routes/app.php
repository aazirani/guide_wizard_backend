<?php
$app->group('/api/app', function () {
    $this->get('/questions', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\AppController:getQuestionList');
    $this->get('/steps', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\AppController:getStepList');
});