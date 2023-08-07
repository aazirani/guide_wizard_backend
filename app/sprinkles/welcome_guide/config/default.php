<?php

    /**
     * Site configuration file for welcome guide
     */
	
    return [
        'address_book' => [
            'admin' => [
                'name'  => 'Admin'
            ]
        ],
        'debug' => [
            'smtp' => true
        ],
        'site' => [
            'author'    =>      'Collegiality',
            'title'     =>      'Welcome Guide',
            // URLs
            'uri' => [
                'author' => 'http://collegiality.de'
            ]
        ],
        'php' => [
            'timezone' => 'Europe/Berlin',
            'log_errors'      => 'true',
            // Let PHP itself render errors natively.  Useful if a fatal error is raised in our custom shutdown handler.
            'display_errors_native' => 'true'
        ]
    ];