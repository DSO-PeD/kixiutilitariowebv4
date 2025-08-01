<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VerifyUserSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $session = DB::table('sessions')
                       ->where('id', $request->session()->getId())
                       ->first();

            // Se a sessão está associada a outro usuário
            if ($session && $session->user_id && $session->user_id != Auth::id()) {
                Auth::logout();
                $request->session()->invalidate();
                return redirect('/login')->withErrors([
                    'message' => 'Sessão inválida detectada. Por favor faça login novamente.'
                ]);
            }

            // Atualizar a sessão com o ID do usuário atual
            DB::table('sessions')
              ->where('id', $request->session()->getId())
              ->update(['user_id' => Auth::id()]);
        }

        return $next($request);
    }
}
