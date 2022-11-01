<?php

$app->group('/steps', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\StepController:pageList')
        ->setName('view_steps');
})->add('authGuard');

$app->group('/api/steps', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\StepController:getList');
	$this->post('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\StepController:create');
	$this->get('/new', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\StepController:createForm');
    /* DELETE */
    $this->delete('/{step_id:[0-9]+}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\StepController:delete');
	/* EDIT */
	$this->get('/{step_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\StepController:editForm');
	$this->post('/{step_id:[0-9]+}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\StepController:update');
})->add('authGuard');