<?php

$app->group('/answers', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\AnswerController:pageList')
        ->setName('view_answers');
    $this->get('/image/{image_name}', 'UserFrosting\Sprinkle\GuideWizard\Controller\AnswerController:deliverImageFile');
})->add('authGuard');

$app->group('/answers', function () {
    $this->get('/image/{image_name}', 'UserFrosting\Sprinkle\GuideWizard\Controller\AnswerController:deliverImageFile');
});

$app->group('/api/answers', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\AnswerController:getList');
	$this->post('', 'UserFrosting\Sprinkle\GuideWizard\Controller\AnswerController:create');
	$this->get('/new', 'UserFrosting\Sprinkle\GuideWizard\Controller\AnswerController:createForm');
    /* DELETE */
    $this->delete('/{answer_id:[0-9]+}', 'UserFrosting\Sprinkle\GuideWizard\Controller\AnswerController:delete');
	/* EDIT */
	$this->get('/{answer_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\GuideWizard\Controller\AnswerController:editForm');
	$this->post('/{answer_id:[0-9]+}', 'UserFrosting\Sprinkle\GuideWizard\Controller\AnswerController:update');
})->add('authGuard');