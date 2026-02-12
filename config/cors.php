<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'artikel/*', 'login', 'register', 'logout'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => [
        'https://artikel.reyhan16.my.id',
        'https://idnbogor.id',
        'https://writing.idnbogor.id',
        'http://127.0.0.1:8000',
        'http://localhost:8000',
    ],

    'allowed_origins_patterns' => [
        '#^https?://.*\.reyhan16\.my\.id$#',
        '#^https?://.*\.idnbogor\.id$#',
    ],

    'allowed_headers' => [
        'Content-Type',
        'X-Requested-With',
        'X-CSRF-TOKEN',
        'Authorization',
        'Accept',
        'Origin',
    ],

    'exposed_headers' => ['X-CSRF-TOKEN'],

    'max_age' => 86400, // 24 hours

    'supports_credentials' => true,

];
