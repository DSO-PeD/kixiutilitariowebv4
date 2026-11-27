<?php

namespace App\Http\Controllers;

use App\Models\ComprovativoModel;
use App\Models\CpvtReconciliacaoModel;
use App\Models\EstadosModel;
use App\Models\RecuperacaoModel;
use App\Models\TKuPendentesModel;
use App\Models\TKxAgenciaModel;
use App\Models\TKxBancoContaModel;
use App\Models\TKxBancoModel;
use App\Models\TKxClProdutoModel;
use App\Models\TKxClTipopagamentoModel;
use App\Models\TKxUsUtilizadorModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use App\Services\IziPayService;
use Illuminate\Support\Facades\Http;



use Illuminate\Support\Facades\Log;


use Exception;
use Illuminate\Support\Facades\DB;


class ClienteCEController extends Controller
{
    //
    public function viewClientesCE(Request $request)
    {
        $authenticatedUser = Auth::user();

        // Obtém a agência e as bases operacionais permitidas ao utilizador logado
        $resultAgenciaUser = TKxAgenciaModel::where('OfCodigo', $authenticatedUser->UtAgencia)->first();

        $basesOperacaoIdentificadores = $resultAgenciaUser
            ? explode(',', $resultAgenciaUser->BasesOperacao)
            : [];

        $basesOperacaoAgencias = TKxAgenciaModel::whereIn('OfIdentificador', $basesOperacaoIdentificadores)
            ->get(['OfCodigo', 'OfIdentificador', 'OfNombre']);

        $codigosAgenciasPermitidas = $basesOperacaoAgencias->pluck('OfCodigo')->toArray();

        // Filtros vindos da request
        $search = $request->input('search', '');
        $agenciaFiltro = $request->input('agencia', 'T');
        $estadoFiltro = (int) $request->input('estado', 0);
        $faseAtribuicao = $request->input('fase_atribuicao', 'TODOS');
        $dataInicio = $request->input('data_inicio', '');
        $dataFim = $request->input('data_fim', '');

        // Query principal sobre tblCaptacoesClientes com joins para nomes de utilizadores e tabelas auxiliares
        $query = DB::table('tblCaptacoesClientes as c')
            ->leftJoin('tkxusutilizador as cap', 'cap.UtCodigo', '=', 'c.UtCodigoCaptador')
            ->leftJoin('tkxusutilizador as ger', 'ger.UtCodigo', '=', 'c.UtCodigoGerente')
            ->leftJoin('tkxusutilizador as sup', 'sup.UtCodigo', '=', 'c.UtCodigoSupervisor')
            ->leftJoin('tkxusutilizador as ofi', 'ofi.UtCodigo', '=', 'c.UtCodigoOficialCredito')
            ->leftJoin('tbltiposnegocio as tn', 'tn.id', '=', 'c.id_tipo_negocio')
            ->leftJoin('tblestadosrespostaagencia as est', 'est.id', '=', 'c.id_estado_resposta_agencia')
            ->select(
                'c.*',
                'cap.UtNome as nome_captador',
                'ger.UtNome as nome_gerente',
                'sup.UtNome as nome_supervisor',
                'ofi.UtNome as nome_oficial',
                'tn.descricao as tipo_negocio_descricao',
                'est.descricao as estado_descricao',
                'est.cor_hex as estado_cor_hex'
            )
            ->whereIn('c.OfCodigoAgencia', $codigosAgenciasPermitidas);

        // Filtro por agência específica
        if ($agenciaFiltro !== 'T' && $agenciaFiltro !== '') {
            $query->where('c.OfCodigoAgencia', (int) $agenciaFiltro);
        }

        // Filtro por estado
        if ($estadoFiltro > 0) {
            $query->where('c.id_estado_resposta_agencia', $estadoFiltro);
        }

        // Filtro por período de data de captação
        if (!empty($dataInicio)) {
            $query->whereDate('c.data_captacao', '>=', $dataInicio);
        }
        if (!empty($dataFim)) {
            $query->whereDate('c.data_captacao', '<=', $dataFim);
        }

        // Filtro por fase de atribuição
        if ($faseAtribuicao === 'SEM_ATRIBUICAO') {
            $query->where(function ($q) {
                $q->whereNull('c.UtCodigoGerente')->orWhere('c.UtCodigoGerente', '');
            })->where(function ($q) {
                $q->whereNull('c.UtCodigoSupervisor')->orWhere('c.UtCodigoSupervisor', '');
            })->where(function ($q) {
                $q->whereNull('c.UtCodigoOficialCredito')->orWhere('c.UtCodigoOficialCredito', '');
            });
        } elseif ($faseAtribuicao === 'GERENTE') {
            $query->whereNotNull('c.UtCodigoGerente')->where('c.UtCodigoGerente', '<>', '');
        } elseif ($faseAtribuicao === 'SUPERVISOR') {
            $query->whereNotNull('c.UtCodigoSupervisor')->where('c.UtCodigoSupervisor', '<>', '');
        } elseif ($faseAtribuicao === 'OFICIAL') {
            $query->whereNotNull('c.UtCodigoOficialCredito')->where('c.UtCodigoOficialCredito', '<>', '');
        } elseif ($faseAtribuicao === 'COM_PARECER') {
            $query->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('tblcaptacaoclientedetalhe as d')
                    ->whereColumn('d.id_captacao_cliente', 'c.id');
            });
        }

        // Filtro de pesquisa textual
        if (!empty($search)) {
            $termo = '%' . $search . '%';
            $query->where(function ($q) use ($termo) {
                $q->where('c.nome_cliente_empresa', 'like', $termo)
                    ->orWhere('c.telefone', 'like', $termo)
                    ->orWhere('c.local_negocio', 'like', $termo)
                    ->orWhere('c.UtCodigoCaptador', 'like', $termo)
                    ->orWhere('c.UtCodigoOficialCredito', 'like', $termo)
                    ->orWhere('cap.UtNome', 'like', $termo)
                    ->orWhere('ofi.UtNome', 'like', $termo);
            });
        }

        $captacoesRaw = $query
            ->orderByDesc('c.data_captacao')
            ->orderByDesc('c.id')
            ->get();

        // Carrega os detalhes/pareceres da tblcaptacaoclientedetalhe agrupados por id_captacao_cliente
        $idsCaptacoes = $captacoesRaw->pluck('id')->toArray();

        $detalhesAgrupados = collect();
        if (!empty($idsCaptacoes)) {
            $detalhesAgrupados = DB::table('tblcaptacaoclientedetalhe as d')
                ->leftJoin('tkxusutilizador as ofi', 'ofi.UtCodigo', '=', 'd.UtCodigoOficial')
                ->leftJoin('tblestadosrespostaagencia as est', 'est.id', '=', 'd.id_estado_resposta_agencia')
                ->select(
                    'd.*',
                    'ofi.UtNome as nome_oficial',
                    'est.descricao as estado_descricao'
                )
                ->whereIn('d.id_captacao_cliente', $idsCaptacoes)
                ->orderBy('d.id', 'asc')
                ->get()
                ->map(function ($det) {
                    return [
                        'id' => $det->id,
                        'id_captacao_cliente' => $det->id_captacao_cliente,
                        'id_estado_resposta_agencia' => $det->id_estado_resposta_agencia,
                        'data_rejeicao' => $det->data_rejeicao,
                        'razao_rejeicao' => $det->razao_rejeicao,
                        'id_quem_rejeitou' => $det->id_quem_rejeitou,
                        'idade' => $det->idade,
                        'numero_bi' => $det->numero_bi,
                        'observacoes' => $det->observacoes,
                        'UtCodigoOficial' => $det->UtCodigoOficial,
                        'OfCodigoAgencia' => $det->OfCodigoAgencia,
                        'created_at' => $det->created_at,
                        'updated_at' => $det->updated_at,
                        'oficial' => $det->nome_oficial ? ['UtNome' => $det->nome_oficial] : null,
                        'estado_resposta' => $det->estado_descricao ? ['descricao' => $det->estado_descricao] : null,
                    ];
                })
                ->groupBy('id_captacao_cliente');
        }

        // Formata a estrutura exata esperada pelo componente Vue ClienteCorp.vue
        $listaCaptacoes = $captacoesRaw->map(function ($item) use ($detalhesAgrupados) {
            $detalhesItem = $detalhesAgrupados->get($item->id, collect())->values()->all();

            return [
                'id' => $item->id,
                'nome_cliente_empresa' => $item->nome_cliente_empresa,
                'genero' => $item->genero,
                'local_negocio' => $item->local_negocio,
                'valor_solicitado' => $item->valor_solicitado,
                'id_tipo_negocio' => $item->id_tipo_negocio,
                'telefone' => $item->telefone,
                'data_captacao' => $item->data_captacao,
                'id_estado_resposta_agencia' => $item->id_estado_resposta_agencia,
                'OfCodigoAgencia' => $item->OfCodigoAgencia,
                'UtCodigoCaptador' => $item->UtCodigoCaptador,
                'UtCodigoSupervisor' => $item->UtCodigoSupervisor,
                'UtCodigoGerente' => $item->UtCodigoGerente,
                'data_atribuicao_gerente' => $item->data_atribuicao_gerente,
                'UtCodigoOficialCredito' => $item->UtCodigoOficialCredito,
                'data_atribuicao_oficial' => $item->data_atribuicao_oficial,
                'data_atribuicao_supervisor' => $item->data_atribuicao_supervisor,
                'atribuido_por' => $item->atribuido_por,
                'ObservacaoEstado' => $item->ObservacaoEstado,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,

                // Objetos relacionados esperados no Vue
                'captador' => $item->nome_captador ? ['UtNome' => $item->nome_captador] : null,
                'gerente' => $item->nome_gerente ? ['UtNome' => $item->nome_gerente] : null,
                'supervisor' => $item->nome_supervisor ? ['UtNome' => $item->nome_supervisor] : null,
                'oficial_credito' => $item->nome_oficial ? ['UtNome' => $item->nome_oficial] : null,
                'tipo_negocio' => $item->tipo_negocio_descricao ? ['descricao' => $item->tipo_negocio_descricao] : null,
                'estado_resposta' => [
                    'descricao' => $item->estado_descricao ?? 'Pendente',
                    'cor_hex' => $item->estado_cor_hex ?? '#f59e0b',
                ],
                'detalhes' => $detalhesItem,
                'detalhe' => !empty($detalhesItem) ? end($detalhesItem) : null,
            ];
        });

        // Carrega os estados e tipos de negócio para os selects de filtro
        $estados = DB::table('tblestadosrespostaagencia')
            ->select('id', 'descricao', 'cor_hex')
            ->get();

        $tiposNegocio = DB::table('tbltiposnegocio')
            ->select('id', 'descricao')
            ->get();

        return Inertia::render('ClienteCorp', [
            'lista_captacoes' => $listaCaptacoes,
            'estados' => $estados,
            'tipos_negocio' => $tiposNegocio,
            'bases' => $basesOperacaoAgencias,
            'filters' => [
                'search' => $search,
                'agencia' => $agenciaFiltro,
                'estado' => $estadoFiltro,
                'fase_atribuicao' => $faseAtribuicao,
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
            ],
            'page' => (int) $request->input('page', 1),
            'perPage' => 50,
        ]);
    }
}
