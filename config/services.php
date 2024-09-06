<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'google' => [
        'client_id' => '70345024332-cscs1nl23ifotd7g5cg9uli9ebarqqgp.apps.googleusercontent.com',
        'client_secret' => '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',
        'redirect' => 'http://127.0.0.1:8000/auth/google/callback',
    ],
    'ghn' => [
        'tokenAPI' => 'dad593b8-5165-11ef-ada2-4270ba03c110',
        'shopId' => 5238093,
    ],

];
