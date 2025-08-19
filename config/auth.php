<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Authentication Guard
    |--------------------------------------------------------------------------
    |
    | Aqui definimos qual o "guard" padrão de autenticação.
    | Mantemos o "web" para sessão normal de browser.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | O guard "web" usa o driver "session" e o provider "users".
    | O guard "api" pode ser configurado se usares Sanctum/JWT.
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Aqui definimos qual model está associado ao provider "users".
    | Troquei o User::class para TKxUsUtilizadorModel::class.
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\TKxUsUtilizadorModel::class,
        ],

        // Se usares outro provider baseado em DB puro (não Eloquent):
        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'tkxusutilizador',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Reset
    |--------------------------------------------------------------------------
    |
    | Isto é usado para reset de senhas. Se não usares reset por email,
    | podes até ignorar ou adaptar depois.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Tempo (em segundos) para expirar confirmação de password.
    | 10800s = 3 horas.
    |
    */

    'password_timeout' => 10800,

];
