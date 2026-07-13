<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Clockwork\Support\Laravel\ClockworkMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Trust Proxies (Cloudflare)
        $middleware->trustProxies(at: '*');
        $middleware->trustProxies(headers: 
            \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
        );

        // Middleware global (executado em todas as requisições web)
        // Middleware global com rate limiting
        $middleware->web(append: [
            HandleInertiaRequests::class,
           //   \Illuminate\Routing\Middleware\ThrottleRequests::class.':60,1', // 60 req/min
            // \App\Http\Middleware\GeoBlockMiddleware::class, // Adicionado aqui
          //  \App\Http\Middleware\EnsureFreshSession::class,
             \App\Http\Middleware\VerifyUserSession::class,
              \Clockwork\Support\Laravel\ClockworkMiddleware::class

        ] );

        // Registra o GeoBlockMiddleware como um middleware de rota (nomeado)
        $middleware->alias([
            'geoblock' => \App\Http\Middleware\GeoBlockMiddleware::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'mobile'   => \App\Http\Middleware\IsMobileApp::class,

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
