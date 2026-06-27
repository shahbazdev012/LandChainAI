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

    /*
    |--------------------------------------------------------------------------
    | Gemini Vision (optional AI document cross-check)
    |--------------------------------------------------------------------------
    |
    | When a key is present the verifier sends the uploaded document image plus
    | the registry's ground-truth data to Gemini and asks it to judge whether
    | the document matches and looks authentic. When absent, verification falls
    | back to OCR + rule-based checks only.
    */

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        'enabled' => env('GEMINI_ENABLED', true),
        'timeout' => (int) env('GEMINI_TIMEOUT', 30),
        'confidence_threshold' => (float) env('GEMINI_CONFIDENCE_THRESHOLD', 0.6),
    ],

];
