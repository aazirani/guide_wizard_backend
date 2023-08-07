<?php

$app->group('/questions', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\QuestionController:pageList')
        ->setName('view_questions');
})->add('authGuard');

$app->group('/api/questions', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\QuestionController:getList');
	$this->post('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\QuestionController:create');
	$this->get('/new', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\QuestionController:createForm');
    /* DELETE */
    $this->delete('/{question_id:[0-9]+}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\QuestionController:delete');
	/* EDIT */
	$this->get('/{question_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\QuestionController:editForm');
	$this->post('/{question_id:[0-9]+}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\QuestionController:update');
})->add('authGuard');