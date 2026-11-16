<?php

namespace App\Http\Controllers;

use App\Models\CaptacaoClienteModel;
use App\Models\EstadoRespostaAgenciaModel;
use App\Models\TipoNegocioModel;
use App\Models\TKxUsUtilizadorModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CaptacaoClienteController extends Controller
{
    //


    /**
     * Carregar opções dos comboboxes (Tipos de Negócio e Estados da Agência)
     */
    public function dadosAuxiliaresCaptacao()
    {
        try {
            $tiposNegocio = TipoNegocioModel::where('ativo', 1)
                ->orderBy('id', 'asc')
                ->get(['id', 'descricao']);

            $estadosAgencia = EstadoRespostaAgenciaModel::where('ativo', 1)
                ->orderBy('id', 'asc')
                ->get(['id', 'descricao', 'cor_hex']);

            return response()->json([
                'status' => 'SUCCESS',
                'tipos_negocio' => $tiposNegocio,
                'estados_agencia' => $estadosAgencia,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Erro ao carregar dados auxiliares: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Gravar nova captação (com validação exists nas tabelas relacionais)
     */
    public function guardarmobilecaptacao(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome_cliente_empresa' => 'required|string|max:150',
            'genero' => 'required|in:M,F,m,f',
            'local_negocio' => 'required|string|max:300',
            'valor_solicitado' => 'required|numeric|min:0',
            'id_tipo_negocio' => 'required|integer|exists:tblTiposNegocio,id',
            'telefone' => 'required|string|max:20',
            'data_captacao' => 'required|date',
            'id_estado_resposta_agencia' => 'nullable|integer|exists:tblEstadosRespostaAgencia,id',
            'OfCodigoAgencia' => 'required|integer',
            'UtCodigoCaptador' => 'required|string|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Erro de validação nos dados enviados.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $captacao = CaptacaoClienteModel::create([
                'nome_cliente_empresa' => $request->input('nome_cliente_empresa'),
                'genero' => strtoupper($request->input('genero')),
                'local_negocio' => $request->input('local_negocio'),
                'valor_solicitado' => $request->input('valor_solicitado'),
                'id_tipo_negocio' => (int) $request->input('id_tipo_negocio'),
                'telefone' => $request->input('telefone'),
                'data_captacao' => $request->input('data_captacao'),
                'id_estado_resposta_agencia' => (int) ($request->input('id_estado_resposta_agencia') ?? 1),
                'OfCodigoAgencia' => (int) $request->input('OfCodigoAgencia'),
                'UtCodigoCaptador' => $request->input('UtCodigoCaptador'),
            ]);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Potencial cliente registado com sucesso!',
                'captacao' => $captacao,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Erro interno ao gravar captação: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Listar captações já trazendo os relacionamentos (tipoNegocio e estadoResposta)
     */
    public function listarmobilecaptacao(Request $request)
    {
        try {
            $query = CaptacaoClienteModel::with(['tipoNegocio', 'estadoResposta'])
                ->orderBy('data_captacao', 'desc')
                ->orderBy('id', 'desc');

            if ($request->filled('UtCodigoCaptador')) {
                $query->where('UtCodigoCaptador', $request->input('UtCodigoCaptador'));
            } elseif ($request->filled('OfCodigoAgencia')) {
                $query->where('OfCodigoAgencia', $request->input('OfCodigoAgencia'));
            }

            $lista = $query->get();

            return response()->json([
                'status' => 'SUCCESS',
                'listaclientes' => $lista,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Erro ao listar clientes captados: ' . $e->getMessage(),
            ], 500);
        }
    }

public function listarOficiaisPorAgencia(Request $request)
{
    $ofCodigoAgencia = $request->input('OfCodigoAgencia');

    $oficiais = TKxUsUtilizadorModel::where('UtAgencia', $ofCodigoAgencia)
        ->where('activo', 1)
        ->where(function ($query) {
            $query->where('UtFuncao', 'LIKE', '%Oficial%')
                  ->orWhere('UtFuncao', 'LIKE', '%Credito%')
                  ->orWhere('UtFuncao', 'LIKE', '%Crédito%')
                  ->orWhere('UtFuncao', 'LIKE', '%Oficial de Crédito%');
        })
        ->select('UtCodigo', 'UtNome', 'UtFuncao', 'UtAgencia')
        ->orderBy('UtNome', 'asc')
        ->get();

    return response()->json([
        'status'   => 'SUCCESS',
        'oficiais' => $oficiais,
    ], 200);
}

public function atribuirAoGerente(Request $request)
{
    $clientesIds     = $request->input('clientes_ids', []);
    $ofCodigoAgencia = $request->input('OfCodigoAgencia');
    $utCodigoOperador = $request->input('UtCodigo');

    if (empty($clientesIds)) {
        return response()->json([
            'status'  => 'ERROR',
            'message' => 'Nenhum cliente foi selecionado.'
        ], 400);
    }

    $gerente = TKxUsUtilizadorModel::where('UtAgencia', $ofCodigoAgencia)
        ->where('activo', 1)
        ->where('UtFuncao', 'LIKE', '%Gerente%')
        ->first();

    if (!$gerente) {
        return response()->json([
            'status'  => 'ERROR',
            'message' => 'Nenhum Gerente ativo encontrado para esta agência.'
        ], 404);
    }

    CaptacaoClienteModel::atribuirGerente($clientesIds, $gerente->UtCodigo, $utCodigoOperador);

    return response()->json([
        'status'  => 'SUCCESS',
        'message' => "Cliente(s) atribuído(s) com sucesso ao Gerente {$gerente->UtNome}."
    ], 200);
}

public function atribuirAoOficial(Request $request)
{
    $clientesIds      = $request->input('clientes_ids', []);
    $utCodigoOficial  = $request->input('UtCodigoOficial');
    $utCodigoOperador = $request->input('UtCodigo');

    if (empty($clientesIds) || empty($utCodigoOficial)) {
        return response()->json([
            'status'  => 'ERROR',
            'message' => 'Selecione pelo menos um cliente e um Oficial de Crédito.'
        ], 400);
    }

    $oficial = TKxUsUtilizadorModel::where('UtCodigo', $utCodigoOficial)
        ->where('activo', 1)
        ->first();

    if (!$oficial) {
        return response()->json([
            'status'  => 'ERROR',
            'message' => 'Oficial de Crédito não encontrado ou inativo.'
        ], 404);
    }

    CaptacaoClienteModel::atribuirOficial($clientesIds, $oficial->UtCodigo, $utCodigoOperador);

    return response()->json([
        'status'  => 'SUCCESS',
        'message' => "Cliente(s) atribuído(s) ao Oficial {$oficial->UtNome}."
    ], 200);
}


}
