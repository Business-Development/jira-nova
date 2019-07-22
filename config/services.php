<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
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

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'jira' => [

        'host' => env('JIRA_HOST'),
        'username' => env('JIRA_USER'),
        'password' => env('JIRA_PASS'),

        'version' => env('JIRA_VERSION', '7.9.2'),
        'use_v3_rest_api' => env('JIRA_REST_API_V3'),

        'oauth' => [
            'token' => env('JIRA_OAUTH_ACCESS_TOKEN')
        ],

        'cookies' => [
            'enabled' => env('JIRA_COOKIE_AUTH_ENABLED', false),
            'file' => env('JIRA_COOKIE_AUTH_FILE', 'jira-cookie.txt')
        ],

        'logs' => [
            'enabled' => env('JIRA_LOG_ENABLED', true),
            'level' => env('JIRA_LOG_LEVEL', 'WARNING'),
            'file' => env('JIRA_LOG_FILE', 'jira-rest-client.log')
        ],

        'curl' => [
            'verify_host' => env('JIRA_CURLOPT_SSL_VERIFYHOST', false),
            'verify_peer' => env('JIRA_CURLOPT_SSL_VERIFYPEER', false),
            'user_agent' => env('JIRA_CURLOPT_USERAGENT', sprintf('curl/%s (%s)', ($curl = curl_version())['version'], $curl['host'])),
            'verbose' => env('JIRA_CURLOPT_VERBOSE', false)
        ],

        'proxy' => [
            'server' => env('JIRA_PROXY_SERVER'),
            'port' => env('JIRA_PROXY_PORT'),
            'user' => env('JIRA_PROXY_USER'),
            'password' => env('JIRA_PROXY_PASSWORD'),
        ]

    ]

];
