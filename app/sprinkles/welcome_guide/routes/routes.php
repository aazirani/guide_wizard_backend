<?php

use UserFrosting\Sprinkle\Core\Util\NoCache;

$app->group('/', function () {
    $this->get('', 'UserFrosting\Sprinkle\Admin\Controller\AdminController:pageDashboard')
        ->setName('dashboard');
})->add('authGuard')->add(new NoCache());