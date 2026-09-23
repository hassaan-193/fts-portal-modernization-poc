<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that will be used to send any
    | email messages sent by your application. Alternative mailers may be
    | configured and used as needed throughout the application.
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their transports. You are free to add additional mailers as required.
    |
    | Laravel supports a variety of mail "transport" drivers that can be used
    | when delivering an e-mail. You will specify which one you are using for
    | your mailers below. You are also free to add additional transports.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses",
    |            "postmark", "log", "array", "failover", "roundrobin"
    |
    */

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                'name' => env('MAIL_FROM_NAME', 'Example'),
            ],
        ],

        'accounts' => [
            'transport' => 'smtp',
            'host' => env('ACCOUNTS_MAIL_HOST', 'box2513.bluehost.com'),
            'port' => env('ACCOUNTS_MAIL_PORT', 465),
            'encryption' => env('ACCOUNTS_MAIL_ENCRYPTION', 'ssl'),
            'username' => env('ACCOUNTS_MAIL_USERNAME'),
            'password' => env('ACCOUNTS_MAIL_PASSWORD'),
            'from' => [
                'address' => env('ACCOUNTS_MAIL_FROM_ADDRESS', 'accounts@example.com'),
                'name' => env('ACCOUNTS_MAIL_FROM_NAME', 'Accounts'),
            ],
        ],

        'lpouts' => [
            'transport' => 'smtp',
            'host' => env('LPOUTS_MAIL_HOST', 'smtp.gmail.com'),
            'port' => env('LPOUTS_MAIL_PORT', 587),
            'encryption' => env('LPOUTS_MAIL_ENCRYPTION', 'tls'),
            'username' => env('LPOUTS_MAIL_USERNAME'),
            'password' => env('LPOUTS_MAIL_PASSWORD'),
            'from' => [
                'address' => env('LPOUTS_MAIL_FROM_ADDRESS', 'lpouts@example.com'),
                'name' => env('LPOUTS_MAIL_FROM_NAME', 'LP Outs'),
            ],
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => '/usr/sbin/sendmail -bs',
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'smtp',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all e-mails sent by your application to be sent from
    | the same address. Here, you may specify a name and address that is
    | used globally for all e-mails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    |
    | If you are using Markdown based email rendering, you may configure your
    | theme and component paths here, allowing you to customize the design
    | of the emails. Or, you may simply stick with the Laravel defaults!
    |
    */

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

];
