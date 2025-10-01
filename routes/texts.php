<?php

$app->group('/texts', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TextController:pageList')
        ->setName('view_texts');
})->add('authGuard');

$app->group('/api/texts', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TextController:getList');
	$this->post('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TextController:create');
	$this->get('/new', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TextController:createForm');
    /* DELETE */
    $this->delete('/{text_id:[0-9]+}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TextController:delete');
	/* EDIT */
	$this->get('/{text_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TextController:editForm');
	$this->post('/{text_id:[0-9]+}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\TextController:update');
})->add('authGuard');