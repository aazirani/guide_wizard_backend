<?php

$app->group('/textSettings', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TextSettingController:pageList')
        ->setName('view_textSettings');
})->add('authGuard');

$app->group('/api/textSettings', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TextSettingController:getList');
    /* EDIT */
    $this->get('/{textSetting_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TextSettingController:editForm');
    $this->post('/{textSetting_id:[0-9]+}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TextSettingController:update');
})->add('authGuard');