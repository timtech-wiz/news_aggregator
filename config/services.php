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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'newsapi' => [
        'uri' => env('NEWS_API_URI', 'https://newsapi.org/v2/everything'),
        'key' => env('NEWS_API_KEY'),
        'timeout' => env('NEWS_API_TIMEOUT', 30),
    ],

    'gnews' => [
        'uri' => env('GNEWS_API_URI', 'https://gnews.io/api/v4/search'),
        'key' => env('GNEWS_API_KEY'),
        'timeout' => env('GNEWS_API_TIMEOUT', 30),
    ],

    'thenewsapi' => [
        'uri' => env('THE_NEWS_API_URI', 'https://api.thenewsapi.com/v1/news/all'),
        'key' => env('THE_NEWS_API_KEY'),
        'timeout' => env('THE_NEWS_API_TIMEOUT', 30),
    ],

];
