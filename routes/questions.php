<?php

$app->group('/questions', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\QuestionController:pageList')
        ->setName('view_questions');
})->add('authGuard');

$app->group('/api/questions', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\QuestionController:getList');
	$this->post('', 'UserFrosting\Sprinkle\GuideWizard\Controller\QuestionController:create');
	$this->get('/new', 'UserFrosting\Sprinkle\GuideWizard\Controller\QuestionController:createForm');
    /* DELETE */
    $this->delete('/{question_id:[0-9]+}', 'UserFrosting\Sprinkle\GuideWizard\Controller\QuestionController:delete');
	/* EDIT */
	$this->get('/{question_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\GuideWizard\Controller\QuestionController:editForm');
	$this->post('/{question_id:[0-9]+}', 'UserFrosting\Sprinkle\GuideWizard\Controller\QuestionController:update');
})->add('authGuard');