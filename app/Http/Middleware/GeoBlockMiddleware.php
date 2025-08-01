<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class GeoBlockMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $allowedCountry = 'AO'; // Apenas Angola

        try {
            $countryCode = $this->getCountryCode();

            if ($countryCode !== $allowedCountry) {
                return response()->json([
                    'error' => 'Este serviço está disponível apenas em Angola',
                    'country_detected' => $countryCode ?? 'Não identificado'
                ], 403);
            }

        } catch (\Exception $e) {
            // Falha silenciosa (permite acesso)
            report($e);
        }

        return $next($request);
    }

    protected function getCountryCode()
    {
        $ip = request()->ip();

        // 1. Consulta direta à API mais confiável
        $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=countryCode");

        if ($response->successful()) {
            return $response->json('countryCode');
        }

        // 2. Fallback para API alternativa (ipinfo.io)
        if (config('services.ipinfo.token')) {
            $response = Http::timeout(3)
                ->get("https://ipinfo.io/{$ip}/json?token=".config('services.ipinfo.token'));

            return $response->json('country');
        }

        return null;
    }
}
