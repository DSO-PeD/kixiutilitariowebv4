<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsMobileApp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se o header X-From-App existe e é 'mobile'
        if ($request->header('X-From-App') !== 'mobile') {
            return response()->json([
                'status' => 'FAILED',
                'message' => 'Acesso permitido apenas para KxUtilitario mobile'
            ], 403);
        }
        return $next($request);
    }
}
