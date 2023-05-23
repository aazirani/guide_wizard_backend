<?php

$app->group('/tasks', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TaskController:pageList')
        ->setName('view_tasks');
})->add('authGuard');

$app->group('/api/tasks', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TaskController:getList');
	$this->post('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TaskController:create');
	$this->get('/new', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TaskController:createForm');
    /* DELETE */
    $this->delete('/{task_id:[0-9]+}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TaskController:delete');
	/* EDIT */
	$this->get('/{task_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TaskController:editForm');
	$this->post('/{task_id:[0-9]+}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TaskController:update');
})->add('authGuard');