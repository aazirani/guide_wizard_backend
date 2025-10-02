<?php

/**
 * Site configuration file for guide wizard
 */

return [
    'address_book' => [
        'admin' => [
            'name' => 'Admin'
        ]
    ],
    'debug' => [
        'smtp' => true
    ],
    'site' => [
        'author' => 'Amin Azirani',
        'title' => 'Guide Wizard',
        // URLs
        'uri' => [
            'author' => 'https://aminazirani.com'
        ]
    ],
    'php' => [
        'timezone' => 'Europe/Berlin',
        'log_errors' => 'false',
        // Let PHP itself render errors natively.  Useful if a fatal error is raised in our custom shutdown handler.
        'display_errors_native' => 'false'
    ]
];