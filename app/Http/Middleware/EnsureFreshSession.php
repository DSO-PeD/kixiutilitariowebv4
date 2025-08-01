<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureFreshSession
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            // Se detectar resquícios de sessão anterior
            if ($request->session()->get('last_user') &&
                $request->session()->get('last_user') !== Auth::id()) {

                session()->flush();
                session()->regenerate();
            }

            // Marca o usuário atual na sessão
            $request->session()->put('last_user', Auth::id());
        }

        return $next($request);
    }
}
