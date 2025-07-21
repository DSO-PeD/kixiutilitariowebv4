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
        $middleware->web(append: [
            HandleInertiaRequests::class,
            // \App\Http\Middleware\GeoBlockMiddleware::class, // Adicionado aqui
        ]);

        // Registra o GeoBlockMiddleware como um middleware de rota (nomeado)
        $middleware->alias([
            'geoblock' => \App\Http\Middleware\GeoBlockMiddleware::class,
        ]);



        // Middleware para APIs stateful (Sanctum)
        $middleware->statefulApi();

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
