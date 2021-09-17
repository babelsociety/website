<?php
return [
    'db' => [
        'name' => getenv('DB_NAME'),
        'user' => getenv('DB_USER'),
        'pswd' => getenv('DB_PSWD'),
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => getenv('DB_PORT') ?: '3306',
    ],

    'mailchimp' => [
        'listId' => getenv('MAILCHIMP_LIST_ID'),
        'apiKey' => getenv('MAILCHIMP_API_KEY'),
        'server' => getenv('MAILCHIMP_SERVER'),
    ],

    'captcha' => [
        'secret' => getenv('RECAPTCHA_SECRET'),
        'hostname' => getenv('RECAPTCHA_HOST'),
    ],
];
