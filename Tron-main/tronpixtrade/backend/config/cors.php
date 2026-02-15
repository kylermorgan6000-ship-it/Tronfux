<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure CORS settings for your application. The values
    | provided here control the Access-Control-* headers that are sent in
    | responses to cross-origin requests.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('FRONTEND_URL', env('APP_URL')),
        'http://localhost:5173',
        'http://localhost:3000',
        'http://localhost:8000',
        'http://127.0.0.1:5173',
        'http://127.0.0.1:3000',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [
        'Authorization',
        'X-Total-Count',
        'X-Total-Pages',
        'X-Per-Page',
        'X-From-Item',
        'X-To-Item',
        'X-Page-Count',
    ],

    'max_age' => 3600,

    'supports_credentials' => true,
];
