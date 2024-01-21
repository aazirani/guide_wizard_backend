<?php

$app->group('/languages', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\LanguageController:pageList')
        ->setName('view_languages');
})->add('authGuard');

$app->group('/api/languages', function () {
    $this->get('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\LanguageController:getList');
	$this->post('', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\LanguageController:create');
	$this->get('/new', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\LanguageController:createForm');
    /* DELETE */
    $this->delete('/{language_id:[0-9]+}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\LanguageController:delete');
	/* EDIT */
	$this->get('/{language_id:[0-9]+}/edit', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\LanguageController:editForm');
	$this->post('/{language_id:[0-9]+}', 'UserFrosting\Sprinkle\WelcomeGuide\Controller\LanguageController:update');
})->add('authGuard');