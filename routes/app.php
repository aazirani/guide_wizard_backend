<?php
$app->group('/api/app', function () {
    $this->get('/translations', 'UserFrosting\Sprinkle\GuideWizard\Controller\AppController:getTranslations');
    $this->get('/content/answerIds[/{answerIds:.*}]', 'UserFrosting\Sprinkle\GuideWizard\Controller\AppController:getStepList');
    $this->get('/lastUpdates', 'UserFrosting\Sprinkle\GuideWizard\Controller\AppController:getLastUpdatedAt');
});