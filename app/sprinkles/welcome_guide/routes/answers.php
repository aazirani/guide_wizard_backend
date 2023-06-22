<?php

$app->group('/answers', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\AnswerController:pageList')
        ->setName('view_answers');
    $this->get('/image/{image_name}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\AnswerController:deliverImageFile');
})->add('authGuard');

$app->group('/api/answers', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\AnswerController:getList');
	$this->post('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\AnswerController:create');
	$this->get('/new', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\AnswerController:createForm');
    /* DELETE */
    $this->delete('/{answer_id:[0-9]+}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\AnswerController:delete');
	/* EDIT */
	$this->get('/{answer_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\AnswerController:editForm');
	$this->post('/{answer_id:[0-9]+}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\AnswerController:update');
})->add('authGuard');