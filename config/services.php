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
    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => 'http://example.com/callback-url',
    ],
    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect' => env('APP_URL') . '/auth/twitter/callback',
        // 'redirect' => 'https://la.webdevprojects.cloud/stackedup/auth/twitter/callback',
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_APP_ID'),
        'client_secret' => env('FACEBOOK_APP_SECRET'),
        'redirect' => env('APP_URL') . '/auth/facebook/callback',
        'default_graph_version' => 'v2.11',
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('APP_URL') . '/auth/google/callback',
    ],
    'linkedin' => [
        'client_id' => env('LINKEDIN_ID'),
        'client_secret' => env('LINKEDIN_ID_SECRET'),
        'redirect' => env('APP_URL') . '/auth/linkedin/callback',
    ],
    'instagrambasic' => [
        'client_id' => env('INSTAGRAMBASIC_CLIENT_ID'),
        'client_secret' => env('INSTAGRAMBASIC_CLIENT_SECRET'),
        'redirect' => 'https://la.webdevprojects.cloud/stackedup/auth/instagram/callback',
        // 'redirect' => env('APP_URL') . '/auth/instagram/callback',
    ],
];


// https://la.webdevprojects.cloud/stackedup/auth/linkedin/callback