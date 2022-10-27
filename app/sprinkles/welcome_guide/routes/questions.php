<?php

$app->group('/questions', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\questionController:pageList')
        ->setName('view_questions');
})->add('authGuard');

$app->group('/api/questions', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\questionController:getList');
	$this->post('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\questionController:create');
	$this->get('/new', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\questionController:createForm');
    /* DELETE */
    $this->delete('/{question_id:[0-9]+}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\questionController:delete');
	/* EDIT */
	$this->get('/{question_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\questionController:editForm');
	$this->post('/{question_id:[0-9]+}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\questionController:update');
})->add('authGuard');