<?php

$app->group('/textSettings', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\TextSettingController:pageList')
        ->setName('view_textSettings');
})->add('authGuard');

$app->group('/api/textSettings', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\TextSettingController:getList');
    /* EDIT */
    $this->get('/{textSetting_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\GuideWizard\Controller\TextSettingController:editForm');
    $this->post('/{textSetting_id:[0-9]+}', 'UserFrosting\Sprinkle\GuideWizard\Controller\TextSettingController:update');
})->add('authGuard');