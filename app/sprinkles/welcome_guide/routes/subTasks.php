<?php

$app->group('/subTasks', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\SubTaskController:pageList')
        ->setName('view_subTasks');
})->add('authGuard');

$app->group('/api/subTasks', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\SubTaskController:getList');
	$this->post('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\SubTaskController:create');
	$this->get('/new', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\SubTaskController:createForm');
    /* DELETE */
    $this->delete('/{subTask_id:[0-9]+}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\SubTaskController:delete');
	/* EDIT */
	$this->get('/{subTask_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\SubTaskController:editForm');
	$this->post('/{subTask_id:[0-9]+}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\SubTaskController:update');
})->add('authGuard');