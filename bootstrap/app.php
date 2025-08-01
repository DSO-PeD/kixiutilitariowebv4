<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {


        // Middleware global (executado em todas as requisições web)
        // Middleware global com rate limiting
        $middleware->web(append: [
            HandleInertiaRequests::class,
           //   \Illuminate\Routing\Middleware\ThrottleRequests::class.':60,1', // 60 req/min
            // \App\Http\Middleware\GeoBlockMiddleware::class, // Adicionado aqui
          //  \App\Http\Middleware\EnsureFreshSession::class,
             \App\Http\Middleware\VerifyUserSession::class,

        ] );

        // Registra o GeoBlockMiddleware como um middleware de rota (nomeado)
        $middleware->alias([
            'geoblock' => \App\Http\Middleware\GeoBlockMiddleware::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        ]);




        // Middleware para APIs stateful (Sanctum)
        $middleware->statefulApi();
          // Middleware para APIs com rate limiting mais restrito
         $middleware->api(prepend: [
         //   \Illuminate\Routing\Middleware\ThrottleRequests::class.':30,1', // 30 req/min
        ]);
        // Configuração de CSRF
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'sanctum/csrf-cookie',
            'login',
            'logout',
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
