<?php

$app->group('/steps', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\StepController:pageList')
        ->setName('view_steps');
    $this->get('/image/{image_name}', 'UserFrosting\Sprinkle\GuideWizard\Controller\StepController:deliverImageFile');
})->add('authGuard');

$app->group('/steps', function () {
    $this->get('/image/{image_name}', 'UserFrosting\Sprinkle\GuideWizard\Controller\StepController:deliverImageFile');
});

$app->group('/api/steps', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\StepController:getList');
	$this->post('', 'UserFrosting\Sprinkle\GuideWizard\Controller\StepController:create');
	$this->get('/new', 'UserFrosting\Sprinkle\GuideWizard\Controller\StepController:createForm');
    /* DELETE */
    $this->delete('/{step_id:[0-9]+}', 'UserFrosting\Sprinkle\GuideWizard\Controller\StepController:delete');
	/* EDIT */
	$this->get('/{step_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\GuideWizard\Controller\StepController:editForm');
	$this->post('/{step_id:[0-9]+}', 'UserFrosting\Sprinkle\GuideWizard\Controller\StepController:update');
})->add('authGuard');