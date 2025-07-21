<?php

namespace App\Http\Middleware;

use App\Models\TKxUsUtilizadorModel;
use Closure;
use Illuminate\Http\Request;
use Torann\GeoIP\Facades\GeoIP;
use Symfony\Component\HttpFoundation\Response;

class GeoBlockMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $allowedCountries = ['AO']; // Países permitidos (APENAS ANGOLA)
        $allowedIPs = TKxUsUtilizadorModel::getDJAIP();

        try {
            $ip = $request->ip();

            // Permite localhost e IPs de desenvolvimento
            if (in_array($ip, ['127.0.0.1', '::1'])) {
                return $next($request);
            }

            // Remove entradas vazias (caso alguém digite "IP1,,IP2" no .env)
            $allowedIPs = array_filter($allowedIPs);

            // Libera IPs na lista branca (se houver IPs permitidos)
            if (!empty($allowedIPs) && in_array($ip, $allowedIPs)) {
                return $next($request);
            }
            // Obtém o país usando Torann\GeoIP
            $location = GeoIP::getLocation($ip);

            // Bloqueia se o país não estiver na lista permitida
            if (!in_array($location->iso_code, $allowedCountries)) {
                // Resposta JSON (para APIs)
                return response()->json(['error' => 'Acesso não permitido no seu país.'], 403);

                // Ou redireciona para uma rota de bloqueio (Inertia/Vue)
                // return redirect()->route('geo.blocked');
            }
        } catch (\Exception $e) {
            // Em caso de erro (ex: falha na API GeoIP), bloqueia ou permite
            if (config('geoip.block_on_failure', true)) {

                return response()->json(['error' => 'Acesso não permitido.', 'DJAIP' => $request->ip()], 403);
            }
        }

        return $next($request);
    }
}
