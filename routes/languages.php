<?php

$app->group('/languages', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\LanguageController:pageList')
        ->setName('view_languages');
})->add('authGuard');

$app->group('/api/languages', function () {
    $this->get('', 'UserFrosting\Sprinkle\GuideWizard\Controller\LanguageController:getList');
	$this->post('', 'UserFrosting\Sprinkle\GuideWizard\Controller\LanguageController:create');
	$this->get('/new', 'UserFrosting\Sprinkle\GuideWizard\Controller\LanguageController:createForm');
    /* DELETE */
    $this->delete('/{language_id:[0-9]+}', 'UserFrosting\Sprinkle\GuideWizard\Controller\LanguageController:delete');
	/* EDIT */
	$this->get('/{language_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\GuideWizard\Controller\LanguageController:editForm');
	$this->post('/{language_id:[0-9]+}', 'UserFrosting\Sprinkle\GuideWizard\Controller\LanguageController:update');
})->add('authGuard');