<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Environment-Specific Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration values that change based on the
    | environment (local, staging, production). These values are loaded
    | after the main config files and can override default settings.
    |
    */

    'local' => [
        'debug' => true,
        'log_level' => 'debug',
        'cache_driver' => 'file',
        'session_driver' => 'file',
        'queue_connection' => 'sync',
        'mail_driver' => 'log',
        'optimize' => false,
        'horizon' => false,
        'queue_workers' => 1,
        'opcache' => false,
        'vite_dev_server' => true,
    ],

    'staging' => [
        'debug' => false,
        'log_level' => 'debug',
        'cache_driver' => 'redis',
        'session_driver' => 'redis',
        'queue_connection' => 'redis',
        'mail_driver' => 'log',
        'optimize' => true,
        'horizon' => false,
        'queue_workers' => 2,
        'opcache' => true,
        'vite_dev_server' => false,
    ],

    'production' => [
        'debug' => false,
        'log_level' => 'error',
        'cache_driver' => 'redis',
        'session_driver' => 'redis',
        'queue_connection' => 'redis',
        'mail_driver' => 'smtp',
        'optimize' => true,
        'horizon' => true,
        'queue_workers' => 4,
        'opcache' => true,
        'vite_dev_server' => false,
    ],
];