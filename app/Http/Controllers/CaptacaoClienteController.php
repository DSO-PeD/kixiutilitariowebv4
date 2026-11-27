<?php

namespace App\Http\Controllers;

use App\Models\CaptacaoClienteModel;
use App\Models\EntidadesRejeicaoModel;
use App\Models\EstadoRespostaAgenciaModel;
use App\Models\TblCaptacaoClienteDetalheModel;
use App\Models\TipoNegocioModel;
use App\Models\TKxUsUtilizadorModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CaptacaoClienteController extends Controller
{
    /**
     * Carregar opções dos comboboxes (Tipos de Negócio, Estados da Agência e Entidades de Rejeição)
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

            $entidadesRejeicao = EntidadesRejeicaoModel::where('ativo', 1)
                ->orderBy('id', 'asc')
                ->get(['id', 'descricao']);

            return response()->json([
                'status' => 'SUCCESS',
                'tipos_negocio' => $tiposNegocio,
                'estados_agencia' => $estadosAgencia,
                'entidades_rejeicao' => $entidadesRejeicao,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Erro ao carregar dados auxiliares: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function oficiaisPorAgencia(Request $request)
    {
        $request->validate([
            'OfCodigoAgencia' => 'required|integer',
        ]);

        $agencia = (int) $request->input('OfCodigoAgencia');

        try {
            // Ajuste o nome da tabela/Model e o filtro de função conforme a sua base de dados
            // (use exatamente a mesma estrutura da query de supervisoresPorAgencia)
            $oficiais = DB::table('utilizadores') // ou Model Utilizador::query()
                ->select('UtCodigo', 'UtNome', 'UtFuncao', 'UtAgencia')
                ->where('UtAgencia', $agencia)
                ->where(function ($query) {
                    $query->where('UtFuncao', 'LIKE', '%Oficial%');
                    // Caso filtre por ID de perfil/função em vez de texto, substitua acima por:
                    // $query->where('id_funcao', ID_DO_OFICIAL);
                })
                ->where('UtEstado', 'A') // Remova ou ajuste se a coluna de estado ativo tiver outro nome
                ->orderBy('UtNome', 'asc')
                ->get();

            return response()->json([
                'status' => 'SUCCESS',
                'oficiais' => $oficiais,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Erro ao consultar oficiais de crédito: ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Gravar nova captação
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
                'id_estado_resposta_agencia' => (int) ($request->input('id_estado_resposta_agencia') ?? 7),
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
     * Alterar dados base do cliente captado
     */
    public function alterarmobilecaptacao(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'nome_cliente_empresa' => 'required|string|max:150',
            'genero' => 'required|in:M,F,m,f',
            'local_negocio' => 'required|string|max:300',
            'valor_solicitado' => 'required|numeric|min:0',
            'id_tipo_negocio' => 'required|integer|exists:tblTiposNegocio,id',
            'telefone' => 'required|string|max:20',
            'data_captacao' => 'required|date',
            'id_estado_resposta_agencia' => 'nullable|integer|exists:tblEstadosRespostaAgencia,id',
            'OfCodigoAgencia' => 'required|integer',
            'UtCodigo' => 'nullable|string|max:150',
            'UtCodigoCaptador' => 'nullable|string|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Erro de validação nos dados enviados.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $captacao = CaptacaoClienteModel::find((int) $request->input('id'));

            if (!$captacao) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Registo de captação não encontrado.',
                ], 404);
            }

            $dadosAtualizar = [
                'nome_cliente_empresa' => trim($request->input('nome_cliente_empresa')),
                'genero' => strtoupper($request->input('genero')),
                'local_negocio' => trim($request->input('local_negocio')),
                'valor_solicitado' => $request->input('valor_solicitado'),
                'id_tipo_negocio' => (int) $request->input('id_tipo_negocio'),
                'telefone' => $request->input('telefone'),
                'data_captacao' => $request->input('data_captacao'),
                'id_estado_resposta_agencia' => (int) ($request->input('id_estado_resposta_agencia') ?? $captacao->id_estado_resposta_agencia ?? 1),
                'OfCodigoAgencia' => (int) $request->input('OfCodigoAgencia'),
            ];

            if ($request->filled('UtCodigoCaptador')) {
                $dadosAtualizar['UtCodigoCaptador'] = $request->input('UtCodigoCaptador');
            }

            $captacao->update($dadosAtualizar);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Dados do potencial cliente atualizados com sucesso!',
                'captacao' => $captacao->fresh(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Erro interno ao atualizar captação: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Guardar ou Atualizar o preenchimento/parecer do Oficial de Crédito (tblCaptacaoClienteDetalhe)
     */
    public function guardarAnaliseOficial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_captacao_cliente' => 'required|integer|exists:tblCaptacoesClientes,id',
            'id_estado_resposta_agencia' => 'required|integer|min:1',
            'idade' => 'required|integer|min:18|max:100',
            'numero_bi' => 'required|string|min:10|max:14',
            'observacoes' => 'nullable|string|max:500',
            'UtCodigoOficial' => 'required|string|max:50',
            'OfCodigoAgencia' => 'required|integer|min:1',
            // Campos obrigatórios apenas se o estado for Rejeitado (ID 10)
            'data_rejeicao' => 'nullable|required_if:id_estado_resposta_agencia,10|date',
            'id_quem_rejeitou' => 'nullable|required_if:id_estado_resposta_agencia,10|integer|min:1',
            'razao_rejeicao' => 'nullable|required_if:id_estado_resposta_agencia,10|string|max:500',
        ], [
            'id_captacao_cliente.exists' => 'O registo de captação selecionado não foi encontrado.',
            'idade.min' => 'A idade do cliente deve ser igual ou superior a 18 anos.',
            'idade.max' => 'A idade do cliente não pode ultrapassar os 100 anos.',
            'numero_bi.min' => 'O número do Bilhete de Identidade deve ter pelo menos 10 caracteres.',
            'data_rejeicao.required_if' => 'A data da rejeição é obrigatória quando o estado é Rejeitado.',
            'id_quem_rejeitou.required_if' => 'Informe quem rejeitou o processo.',
            'razao_rejeicao.required_if' => 'A razão da rejeição é obrigatória.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Verifique os dados submetidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $idCaptacao = (int) $request->input('id_captacao_cliente');
        $idEstado = (int) $request->input('id_estado_resposta_agencia');
        $isRejeitado = ($idEstado === 10);

        try {
            DB::beginTransaction();

            // 1. Cria ou atualiza o parecer técnico em tblCaptacaoClienteDetalhe
            $detalhe = TblCaptacaoClienteDetalheModel::updateOrCreate(
                ['id_captacao_cliente' => $idCaptacao],
                [
                    'id_estado_resposta_agencia' => $idEstado,
                    'data_rejeicao' => $isRejeitado ? $request->input('data_rejeicao') : null,
                    'razao_rejeicao' => $isRejeitado ? trim((string) $request->input('razao_rejeicao')) : null,
                    'id_quem_rejeitou' => $isRejeitado ? (int) $request->input('id_quem_rejeitou') : null,
                    'idade' => (int) $request->input('idade'),
                    'numero_bi' => strtoupper(trim((string) $request->input('numero_bi'))),
                    'observacoes' => trim((string) $request->input('observacoes', '')),
                    'UtCodigoOficial' => trim((string) $request->input('UtCodigoOficial')),
                    'OfCodigoAgencia' => (int) $request->input('OfCodigoAgencia'),
                ]
            );

            // 2. Atualiza o estado e a observação na tabela principal tblCaptacoesClientes
            $captacao = CaptacaoClienteModel::findOrFail($idCaptacao);
            $captacao->id_estado_resposta_agencia = $idEstado;

            if ($request->filled('observacoes')) {
                $captacao->ObservacaoEstado = trim((string) $request->input('observacoes'));
            }

            // Caso ainda não estivesse atribuído ao oficial que deu o parecer, regista-o
            if (empty($captacao->UtCodigoOficialCredito)) {
                $captacao->UtCodigoOficialCredito = trim((string) $request->input('UtCodigoOficial'));
                $captacao->data_atribuicao_oficial = now();
            }

            $captacao->save();

            DB::commit();

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Parecer do Oficial de Crédito registado com sucesso.',
                'detalhe' => $detalhe,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'ERROR',
                'message' => 'Erro ao guardar o parecer do Oficial de Crédito: ' . $e->getMessage(),
            ], 500);
        }
    }
    /*public function guardarAnaliseOficial(Request $request)
    {
        $idEstado = (int) $request->input('id_estado_resposta_agencia');
        $estado = EstadoRespostaAgenciaModel::find($idEstado);

        // ID 10 = Rejeitado na tblEstadosRespostaAgencia
        $isRejeitado = $estado && ($estado->id == 10 || strtolower(trim($estado->descricao)) === 'rejeitado');

        $rules = [
            'id_captacao_cliente' => 'required|integer|exists:tblcaptacoesclientes,id',
            'id_estado_resposta_agencia' => 'required|integer|exists:tblEstadosRespostaAgencia,id',
            'idade' => 'required|integer|min:18|max:100',
            'numero_bi' => 'required|string|max:30',
            'observacoes' => 'nullable|string|max:500',
            'UtCodigoOficial' => 'required|string|max:50',
            'OfCodigoAgencia' => 'required|integer',
        ];

        if ($isRejeitado) {
            $rules['data_rejeicao'] = 'required|date';
            $rules['razao_rejeicao'] = 'required|string|max:500';
            $rules['id_quem_rejeitou'] = 'required|integer|exists:tblEntidadesRejeicao,id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Verifique os campos obrigatórios.',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $idCaptacao = (int) $request->input('id_captacao_cliente');
            $captacao = CaptacaoClienteModel::findOrFail($idCaptacao);

            $detalhe = TblCaptacaoClienteDetalheModel::updateOrCreate(
                ['id_captacao_cliente' => $idCaptacao],
                [
                    'id_estado_resposta_agencia' => $idEstado,
                    'data_rejeicao' => $isRejeitado ? $request->input('data_rejeicao') : null,
                    'razao_rejeicao' => $isRejeitado ? trim($request->input('razao_rejeicao')) : null,
                    'id_quem_rejeitou' => $isRejeitado ? (int) $request->input('id_quem_rejeitou') : null,
                    'idade' => (int) $request->input('idade'),
                    'numero_bi' => strtoupper(trim($request->input('numero_bi'))),
                    'observacoes' => $request->filled('observacoes') ? trim($request->input('observacoes')) : null,
                    'UtCodigoOficial' => $request->input('UtCodigoOficial'),
                    'OfCodigoAgencia' => (int) $request->input('OfCodigoAgencia'),
                ]
            );

            // Sincroniza o estado e observação na tabela principal tblcaptacoesclientes
            $captacao->update([
                'id_estado_resposta_agencia' => $idEstado,
                'ObservacaoEstado' => $request->filled('observacoes')
                    ? mb_substr(trim($request->input('observacoes')), 0, 300)
                    : $captacao->ObservacaoEstado,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Parecer do Oficial de Crédito registado com sucesso!',
                'detalhe' => $detalhe->load('entidadeRejeicao'),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Erro ao guardar análise: ' . $e->getMessage(),
            ], 500);
        }
    }*/

    /**
     * Listar captações já trazendo os relacionamentos (tipoNegocio, estadoResposta e detalheOficial)
     */
    public function listarmobilecaptacao(Request $request)
    {
        try {
            $query = CaptacaoClienteModel::with([
                'tipoNegocio',
                'estadoResposta',
                'detalheOficial.entidadeRejeicao',
                'captador:UtCodigo,UtNome',
                'gerente:UtCodigo,UtNome',
                'supervisor:UtCodigo,UtNome',
                'oficialCredito:UtCodigo,UtNome',
            ])
                ->orderBy('data_captacao', 'desc')
                ->orderBy('id', 'desc');

            $utCodigo = trim((string) $request->input('UtCodigoCaptador', ''));
            $ofCodigoAgencia = (int) $request->input('OfCodigoAgencia', 0);

            if ($ofCodigoAgencia > 0) {
                $query->where('OfCodigoAgencia', $ofCodigoAgencia);
            }

            // Filtra os clientes ligados ao utilizador (seja como Captador, Oficial, Supervisor ou Gerente)
            if ($utCodigo !== '') {
                $query->where(function ($q) use ($utCodigo) {
                    $q->where('UtCodigoCaptador', $utCodigo)
                        ->orWhere('UtCodigoOficialCredito', $utCodigo)
                        ->orWhere('UtCodigoSupervisor', $utCodigo)
                        ->orWhere('UtCodigoGerente', $utCodigo);
                });
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
            'status' => 'SUCCESS',
            'oficiais' => $oficiais,
        ], 200);
    }

    public function atribuirAoGerente(Request $request)
    {
        $clientesIds = $request->input('clientes_ids', []);
        $ofCodigoAgencia = $request->input('OfCodigoAgencia');
        $utCodigoOperador = $request->input('UtCodigo');

        if (empty($clientesIds)) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Nenhum cliente foi selecionado.'
            ], 400);
        }

        $gerente = TKxUsUtilizadorModel::where('UtAgencia', $ofCodigoAgencia)
            ->where('activo', 1)
            ->where('UtFuncao', 'LIKE', '%Gerente%')
            ->first();

        if (!$gerente) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Nenhum Gerente ativo encontrado para esta agência.'
            ], 404);
        }

        CaptacaoClienteModel::atribuirGerente($clientesIds, $gerente->UtCodigo, $utCodigoOperador);

        return response()->json([
            'status' => 'SUCCESS',
            'message' => "Cliente(s) atribuído(s) com sucesso ao Gerente {$gerente->UtNome}."
        ], 200);
    }

    public function atribuirAoOficial(Request $request)
    {
        $clientesIds = $request->input('clientes_ids', []);
        $utCodigoOficial = $request->input('UtCodigoOficial');
        $utCodigoOperador = $request->input('UtCodigo');

        if (empty($clientesIds) || empty($utCodigoOficial)) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Selecione pelo menos um cliente e um Oficial de Crédito.'
            ], 400);
        }

        $oficial = TKxUsUtilizadorModel::where('UtCodigo', $utCodigoOficial)
            ->where('activo', 1)
            ->first();

        if (!$oficial) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Oficial de Crédito não encontrado ou inativo.'
            ], 404);
        }

        CaptacaoClienteModel::atribuirOficial($clientesIds, $oficial->UtCodigo, $utCodigoOperador);

        return response()->json([
            'status' => 'SUCCESS',
            'message' => "Cliente(s) atribuído(s) ao Oficial {$oficial->UtNome}."
        ], 200);
    }
    /**
     * Lista os Supervisores ativos da agência selecionada.
     */
    public function supervisoresPorAgencia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'OfCodigoAgencia' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Código da agência inválido.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $ofCodigoAgencia = (int) $request->input('OfCodigoAgencia');

        try {
            $supervisores = TKxUsUtilizadorModel::select('UtCodigo', 'UtNome', 'UtFuncao', 'UtAgencia')
                ->where('UtAgencia', $ofCodigoAgencia)
                ->where('activo', 1)
                ->where('UtFuncao', 'LIKE', '%Supervisor%')
                ->orderBy('UtNome', 'asc')
                ->get();

            return response()->json([
                'status' => 'SUCCESS',
                'supervisores' => $supervisores,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Erro ao consultar supervisores da agência: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Atribui um lote de clientes captados a um Supervisor.
     */
    public function atribuirClienteSupervisor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clientes_ids' => 'required|array|min:1',
            'clientes_ids.*' => 'required',
            'UtCodigoSupervisor' => 'required|string|exists:tkxusutilizador,UtCodigo',
            'UtCodigo' => 'required|string',
            'OfCodigoAgencia' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Dados inválidos para atribuição ao Supervisor.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $clientesIds = $request->input('clientes_ids');
            $utCodigoSupervisor = trim($request->input('UtCodigoSupervisor'));
            $utCodigoAtribuidor = trim($request->input('UtCodigo'));

            $atualizados = CaptacaoClienteModel::atribuirSupervisor(
                $clientesIds,
                $utCodigoSupervisor,
                $utCodigoAtribuidor
            );

            return response()->json([
                'status' => 'SUCCESS',
                'message' => "{$atualizados} cliente(s) atribuído(s) ao Supervisor com sucesso!",
                'atualizados' => $atualizados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Erro ao atribuir cliente(s) ao Supervisor: ' . $e->getMessage(),
            ], 500);
        }
    }



}
