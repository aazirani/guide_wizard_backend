<?php

$app->group('/logics', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\LogicController:pageList')
        ->setName('view_logics');
})->add('authGuard');

$app->group('/api/logics', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\LogicController:getList');
	$this->post('', 'UserFrosting\Sprinkle\GuideWizard\Controller\LogicController:create');
	$this->get('/new', 'UserFrosting\Sprinkle\GuideWizard\Controller\LogicController:createForm');
    /* DELETE */
    $this->delete('/{logic_id:[0-9]+}', 'UserFrosting\Sprinkle\GuideWizard\Controller\LogicController:delete');
	/* EDIT */
	$this->get('/{logic_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\GuideWizard\Controller\LogicController:editForm');
	$this->post('/{logic_id:[0-9]+}', 'UserFrosting\Sprinkle\GuideWizard\Controller\LogicController:update');
})->add('authGuard');