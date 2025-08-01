<?php

use Illuminate\Support\Str;


/*
return [


    'driver' => env('SESSION_DRIVER', 'database'),


    'lifetime' => (int) env('SESSION_LIFETIME', 120),

    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),



    'encrypt' => env('SESSION_ENCRYPT', false),



    'files' => storage_path('framework/sessions'),


    'connection' => env('SESSION_CONNECTION'),



    'table' => env('SESSION_TABLE', 'sessions'),



    'store' => env('SESSION_STORE'),


    'lottery' => [2, 100],


    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_session'
    ),



    'path' => env('SESSION_PATH', '/'),



    'domain' => env('SESSION_DOMAIN'),



    'secure' => env('SESSION_SECURE_COOKIE'),



    'http_only' => env('SESSION_HTTP_ONLY', true),


    'same_site' => env('SESSION_SAME_SITE', 'lax'),


    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

];

*/
return [
        'driver' => env('SESSION_DRIVER', 'database'), // Recomendo database ou redis
        'lifetime' => env('SESSION_LIFETIME', 120),
        'expire_on_close' => false,
        'encrypt' => true,
        'files' => storage_path('framework/sessions'),
        'connection' => env('SESSION_CONNECTION', null),
        'table' => 'sessions',
        'store' => env('SESSION_STORE', null),
        'lottery' => [2, 100],
        'cookie' => 'laravel_session',
        'path' => '/',
        'domain' => env('SESSION_DOMAIN', null),
        'secure' => env('SESSION_SECURE_COOKIE', true),
        'http_only' => true,
        'same_site' => 'lax',
];
