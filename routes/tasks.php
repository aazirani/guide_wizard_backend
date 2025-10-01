<?php

$app->group('/tasks', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\TaskController:pageList')
        ->setName('view_tasks');
    $this->get('/image/{image_name}', 'UserFrosting\Sprinkle\GuideWizard\Controller\TaskController:deliverImageFile');
})->add('authGuard');

$app->group('/tasks', function () {
    $this->get('/image/{image_name}', 'UserFrosting\Sprinkle\GuideWizard\Controller\TaskController:deliverImageFile');
});

$app->group('/api/tasks', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\TaskController:getList');
	$this->post('', 'UserFrosting\Sprinkle\GuideWizard\Controller\TaskController:create');
	$this->get('/new', 'UserFrosting\Sprinkle\GuideWizard\Controller\TaskController:createForm');
    /* DELETE */
    $this->delete('/{task_id:[0-9]+}', 'UserFrosting\Sprinkle\GuideWizard\Controller\TaskController:delete');
	/* EDIT */
	$this->get('/{task_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\GuideWizard\Controller\TaskController:editForm');
	$this->post('/{task_id:[0-9]+}', 'UserFrosting\Sprinkle\GuideWizard\Controller\TaskController:update');
})->add('authGuard');