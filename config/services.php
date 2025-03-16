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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Payment Gateway Services
    'chipin' => [
        'api_key' => env('CHIPIN_API_KEY', 'test_key'),
        'api_secret' => env('CHIPIN_API_SECRET', 'test_secret'),
        'api_url' => env('CHIPIN_API_URL', 'https://api.chip-in.asia/v1'),
        'checkout_url' => env('CHIPIN_CHECKOUT_URL', 'https://checkout.chip-in.asia'),
    ],

    'chipinasia' => [
        'key' => env('CHIPINASIA_KEY'),
        'secret' => env('CHIPINASIA_SECRET'),
    ],

    'bank_transfer' => [
        'bank_name' => env('BANK_TRANSFER_BANK_NAME', 'Bank Islam'),
        'account_number' => env('BANK_TRANSFER_ACCOUNT_NUMBER', '12345678901'),
        'account_holder' => env('BANK_TRANSFER_ACCOUNT_HOLDER', 'Masjid Al-Makmur'),
    ],

];
