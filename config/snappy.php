<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Snappy PDF / Image Configuration
    |--------------------------------------------------------------------------
    |
    | This option contains settings for PDF generation.
    |
    | Enabled:
    |
    |    Whether to load PDF / Image generation.
    |
    | Binary:
    |
    |    The file path of the wkhtmltopdf / wkhtmltoimage executable.
    |
    | Timout:
    |
    |    The amount of time to wait (in seconds) before PDF / Image generation is stopped.
    |    Setting this to false disables the timeout (unlimited processing time).
    |
    | Options:
    |
    |    The wkhtmltopdf command options. These are passed directly to wkhtmltopdf.
    |    See https://wkhtmltopdf.org/usage/wkhtmltopdf.txt for all options.
    |
    | Env:
    |
    |    The environment variables to set while running the wkhtmltopdf process.
    |
    */
    'pdf' => [
        'enabled' => true,
        'binary' => env('WKHTMLTOPDF_PATH', (PHP_OS_FAMILY === 'Windows' ? 'wkhtmltopdf.exe' : (file_exists('/usr/local/bin/wkhtmltopdf') ? '/usr/local/bin/wkhtmltopdf' : 'wkhtmltopdf'))),
        'timeout' => false,
        'options' => [
            'enable-local-file-access' => true,
        ],
        'env'     => [],
    ],

    'image' => [
        'enabled' => true,
        'binary' => env('WKHTMLTOIMAGE_PATH', (PHP_OS_FAMILY === 'Windows' ? 'wkhtmltoimage.exe' : (file_exists('/usr/local/bin/wkhtmltoimage') ? '/usr/local/bin/wkhtmltoimage' : 'wkhtmltoimage'))),
        'timeout' => false,
        'options' => [],
        'env'     => [],
    ],

];
