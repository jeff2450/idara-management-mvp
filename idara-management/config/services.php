<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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
    | SMS Gateway (angalia stacks.md §4 na architecture.md §2.4)
    |--------------------------------------------------------------------------
    | 'driver' huchagua implementation ipi ya SmsGatewayInterface itakayotumika
    | (imesajiliwa kwenye App\Providers\SmsServiceProvider). 'log' ni default
    | salama kwa local/testing - haiiti API ya nje, inaandika kwenye log file
    | tu, ili usilazimike kuwa na akaunti ya Beem/NextSMS kuendesha mfumo
    | kwenye mazingira ya maendeleo.
    */
    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'), // 'log' | 'beem' | 'nextsms'

        'beem' => [
            'api_key' => env('BEEM_API_KEY'),
            'secret_key' => env('BEEM_SECRET_KEY'),
            'sender_id' => env('BEEM_SENDER_ID', 'INFO'),
        ],

        'nextsms' => [
            'username' => env('NEXTSMS_USERNAME'),
            'password' => env('NEXTSMS_PASSWORD'),
            'sender_id' => env('NEXTSMS_SENDER_ID', 'INFO'),
        ],
    ],

];
