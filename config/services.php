<?php

return [

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

    // SDSL School Management System API
    'sdsl' => [
        'api_url' => env('SDSL_API_URL', 'https://gov.school.edu.sl/api'),
        'api_key' => env('SDSL_API_KEY', ''),
    ],

    // PeeapPay Payment Gateway
    'peeappay' => [
        'base_url' => env('PEEAPPAY_BASE_URL', 'https://api.peeappay.com/v1'),
        'api_key' => env('PEEAPPAY_API_KEY', ''),
        'merchant_id' => env('PEEAPPAY_MERCHANT_ID', ''),
        'secret_key' => env('PEEAPPAY_SECRET_KEY', ''),
    ],

];
