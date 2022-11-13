<?php
$app->group('/api/app', function () {
    $this->get('/questions', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\AppController:getQuestionList');
});