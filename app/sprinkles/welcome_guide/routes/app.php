<?php
$app->group('/api/app', function () {
    $this->get('/translations', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\AppController:getTranslations');
    $this->get('/content', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\AppController:getStepList');
    $this->get('/lastUpdates', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\AppController:getLastUpdatedAt');
});