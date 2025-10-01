<?php

$app->group('/translations', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\TranslationController:pageList')
        ->setName('view_translations');
})->add('authGuard');

$app->group('/api/translations', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\TranslationController:getList');
	$this->post('', 'UserFrosting\Sprinkle\GuideWizard\Controller\TranslationController:create');
	$this->get('/new', 'UserFrosting\Sprinkle\GuideWizard\Controller\TranslationController:createForm');
    /* DELETE */
    $this->delete('/{translation_id:[0-9]+}', 'UserFrosting\Sprinkle\GuideWizard\Controller\TranslationController:delete');
	/* EDIT */
	$this->get('/{translation_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\GuideWizard\Controller\TranslationController:editForm');
	$this->post('/{translation_id:[0-9]+}', 'UserFrosting\Sprinkle\GuideWizard\Controller\TranslationController:update');
})->add('authGuard');