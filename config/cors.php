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

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],  // You can restrict methods like 'GET, POST' if needed.

    'allowed_origins' => ['http://localhost:3000'],  // Add your frontend's domain here

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],  // Define specific headers if necessary

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,  // Set to true if your requests require credentials

];
