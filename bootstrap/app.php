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

        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
        $middleware->statefulApi([
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'api/*',                // Rotas API
            'sanctum/csrf-cookie',  // Rota do Sanctum para SPAs
            'login',                // Seu endpoint de login
            'logout',               // Seu endpoint de logout
        ]);
        $middleware->statefulApi();
        //
    })
    ->withProviders([
        Laravel\Sanctum\SanctumServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
