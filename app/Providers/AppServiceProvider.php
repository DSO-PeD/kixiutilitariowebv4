<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\{
    App,
    Auth,
    DB,
    Log,
    RateLimiter,
    Request as RequestFacade,
    Response,
    Route,
    URL,
    View
};
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\View\Compilers\BladeCompiler;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registro de serviços específicos para produção
        if (App::environment('production')) {
            $this->app->singleton('cleaner', function () {
                return new class {
                    public function clean()
                    {
                        gc_collect_cycles();
                    }
                };
            });

            $this->app->terminating(fn() => app('cleaner')->clean());
        }
    }

    public function boot(): void
    {
        if (App::environment('production')) {
            $this->applyProductionOptimizations();
        }

        $this->shareDataWithInertia();
        $this->registerRequestMacros();
        $this->registerResponseMacros();
        $this->registerRateLimiting();
        $this->monitorSlowQueries();
    }

    protected function applyProductionOptimizations(): void
    {
        // Força cache de longo prazo em produção
        Route::prependMiddlewareToGroup('web', 'cache.headers:public;max_age=31536000');


    }

    protected function shareDataWithInertia(): void
    {
        Inertia::version(fn() => md5_file(public_path('mix-manifest.json')));

       Inertia::share(
            [
                'auth.user' => function () {
                    return Auth::user();  // Isso retorna os dados do usuário autenticado
                },
                // Outros dados podem ser compartilhados aqui, como dados da sessão
                'session' => function () {
                    //return session()->all();  // Isso retorna todos os dados da sessão
                    return [
                        'agencia_principal' => session('agencia_principal'),
                        'bases_operacionais' => session('bases_operacionais'),
                    ];
                },
                'flash' => function () {
                    return [
                        'success' => session('success'),
                        'error' => session('error'),
                    ];
                },

            ],

        );


    }

    protected function registerRequestMacros(): void
    {
        RequestFacade::macro('wantsInertia', fn() => $this->header('X-Inertia') === 'true');
    }

    protected function registerResponseMacros(): void
    {
        Response::macro(
            'withNoIndex',
            fn($content = '') =>
            response($content)
                ->header('X-Robots-Tag', 'noindex, nofollow')
                ->header('Cache-Control', 'public, max-age=300')
        );
    }

    protected function registerRateLimiting(): void
    {
        RateLimiter::for(
            'login',
            fn(Request $request) =>
            Limit::perMinute(5)->by($request->ip())
        );
    }

    protected function monitorSlowQueries(): void
    {
        DB::listen(function ($query) {
            if ($query->time > 100) {
                Log::channel('slow_queries')->warning('Slow Query', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms',
                ]);
            }
        });
    }
}
