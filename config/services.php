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
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // FCM HTTP v1, used by the payment-booking module. `credentials` is a path
    // to the service-account JSON; a bare filename resolves under storage/app.
    // Note config('services.firebase') below is the LEGACY FCM API, retired by
    // Google in 2024 — unrelated and unused.
    'fcm' => [
        'credentials' => env('FCM_CREDENTIALS'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL'),
        'firebase' => [
            'server_key' => env('FIREBASE_SERVER_KEY'),
            'fcm_push_url' => env('FCM_PUSH_URL'),
        ]
    ],
    'ultramsg' => [
        'instance_id' => env('ULTRAMSG_INSTANCE_ID'),
        'token' => env('ULTRAMSG_TOKEN'),
    ],
    'wasender' => [
    'api_key' => env('WASENDER_API_KEY'),
    ],

];