<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PgtRefNotificacaoController extends Controller
{
    //
    public function carregarPagamentoPorReferencia(Request $request)
    {
        // 1. Validar a access-key
        $accessKey = $request->header('Access-Key') ?? $request->input('access_key');

        if (!$this->validateAccessKey($accessKey)) {
            Log::warning('Tentativa de acesso com chave inválida', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 2. Validar os dados da transação
        $validated = $request->validate([
            'idTransacao' => 'required|string',
            'numLogSistema' => 'required|numeric',
            'idLogSistema' => 'required|numeric',
            'dataTransaccaoCliente' => 'required|datetime',
            'montantePago' => 'required|numeric',
            'tipoTerminal' => 'required|string',
            'iIdentTerminal' => 'required|string',
            'localidadeTerminal' => 'required|string',
            'refPagamento' => 'required|string',
            'Id' => 'required|string'
            // Adicione outros campos conforme documentação
        ]);
    }
    protected function validateAccessKey($accessKey)
    {

        /*
            php artisan tinker
            >>> echo bin2hex(random_bytes(32));
        */
        $validKey = config('djanotifpgtref.callback_access_key');

        return !empty($validKey) && hash_equals($validKey, $accessKey);
    }
}
