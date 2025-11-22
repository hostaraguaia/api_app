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

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'api/v1/webooks'],

    'allowed_methods' => ['*'],


    'allowed_origins' => ['*', 'http://beatify.com.br ', 'http://www.beatify.com.br','http://www.beatify.com.br:80','http://www.beatify.com.br:443', 'https://beatify.com.br', 'https://www.beatify.com.br', 'http://www.beatify.com.br:80', 'https://www.beatify.com.br:443'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
