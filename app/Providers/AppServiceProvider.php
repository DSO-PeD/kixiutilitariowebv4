<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Compartilhando os dados do usuário autenticado com todas as páginas
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
}
