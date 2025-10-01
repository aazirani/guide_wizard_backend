<?php

$app->group('/texts', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\TextController:pageList')
        ->setName('view_texts');
})->add('authGuard');

$app->group('/api/texts', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\TextController:getList');
	$this->post('', 'UserFrosting\Sprinkle\GuideWizard\Controller\TextController:create');
	$this->get('/new', 'UserFrosting\Sprinkle\GuideWizard\Controller\TextController:createForm');
    /* DELETE */
    $this->delete('/{text_id:[0-9]+}', 'UserFrosting\Sprinkle\GuideWizard\Controller\TextController:delete');
	/* EDIT */
	$this->get('/{text_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\GuideWizard\Controller\TextController:editForm');
	$this->post('/{text_id:[0-9]+}', 'UserFrosting\Sprinkle\GuideWizard\Controller\TextController:update');
})->add('authGuard');