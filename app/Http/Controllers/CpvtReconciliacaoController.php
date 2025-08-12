<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\CpvtReconciliacaoModel;
use App\Models\EstadosModel;
use Illuminate\Http\Request;
use App\Models\ComprovativoModel;
use App\Models\TKxAgenciaModel;
use App\Models\TKxBancoContaModel;
use App\Models\TKxBancoModel;
use App\Models\TKxClProdutoModel;
use App\Models\TKxClTipopagamentoModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Clockwork;



class CpvtReconciliacaoController extends Controller
{
    public function viewComprovativosReconlicacao(Request $request)
    {

        // Cache de dados estáticos que raramente mudam
        $cacheKey = 'reconciliacao_static_data_' . Auth::id();
        $staticData = Cache::remember($cacheKey, 3600, function () {
            return [
                'lista_produtos' => TKxClProdutoModel::getProdutos(),
                'lista_das_formaspagamento' => TKxClTipopagamentoModel::getFormasDePamentos(),
                'estados' => EstadosModel::getEstadosDCF('DCF'),
                'lista_banco' => TKxBancoModel::getBancos(),
                'lista_bancos_contas' => TKxBancoContaModel::getBancosContas(),
            ];
        });

        $authenticatedUser = Auth::user();
        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', $authenticatedUser->UtAgencia)->first();

        // Pré-processamento de dados
        $hoje = date('Ymd');
        $dataFecho = date("Y-m-d", strtotime($resultagencia_user->DataFecho));
        $dataActual = date("Y-m-d", strtotime($hoje));
        $sistema_aberto = ($dataFecho != $dataActual);

        $ids_estados = $staticData['estados']->pluck('id')->implode(',');
        $BasesOperacao = explode(',', $resultagencia_user->BasesOperacao);

        // Processamento condicional mais eficiente
        $filterData = $this->processFilters($request, $resultagencia_user, $staticData, $ids_estados, $hoje);

        // Consulta principal com parâmetros já processados
        $lista_comprovativo = ComprovativoModel::getComprovativos(
            $filterData['Bases'],
            $filterData['DataInicio'],
            $filterData['DataFim'],
            $resultagencia_user->NumeroRegistroTabela,
            $filterData['TIPO'],
            $filterData['LOAN'],
            $filterData['ESTADO'],
            $filterData['produtos_geral_busca'],
            $filterData['formaspagamento_geral']
        );

        // Otimização do processamento da lista de comprovativos
        $comprovativos_list = $this->processComprovativosList($lista_comprovativo);

        // Cálculos de totais mais eficientes
        $totals = $this->calculateTotals($lista_comprovativo);

        return Inertia::render('Reconciliacao', [
            'lista_comprovativo' => $comprovativos_list,
            'filters' => $this->getFilterParams($request),
            'page' => (int) $request->input('page', 1),
            'bases' => TKxAgenciaModel::whereIn('OfIdentificador', $BasesOperacao)->get(),
            'produtos' => $staticData['lista_produtos'],
            'bancos' => $staticData['lista_banco'],
            'contas' => $staticData['lista_bancos_contas'],
            'tipocomprovativos' => ['G' => 'G/', 'I' => 'I/'],
            'formaspagamentos' => $staticData['lista_das_formaspagamento'],
            'estados' => $staticData['estados'],
            'total' => count($lista_comprovativo),
            'montantetotal' => $totals['montante_total'],
            'dataInicioPeriodo' => $totals['data_inicio_formatada'],
            'dataFimPeriodo' => $totals['data_fim_formatada'],
            'totalMontantePoupanca' => $totals['montante_poupanca'],


            'totalMontanteRegistado' =>  $totals['totalMontanteRegistado'],
            'totalMontantePoupancaRegistado' =>  $totals['totalMontantePoupancaRegistado'],

            'totalMontanteReflete' => $totals['totalMontanteReflete'],
            'totalMontantePoupancaReflete' => $totals['totalMontantePoupancaReflete'],

            'totalMontanteInregulares' =>  $totals['totalMontanteInregulares'],
            'totalMontantePoupancaInregulares' =>  $totals['totalMontantePoupancaInregulares'],
        ]);
    }

    protected function processFilters($request, $agencia, $staticData, $ids_estados, $hoje)
    {
        $defaults = [
            'Bases' => "'" . $agencia->BasesOperacao . "'",
            'DataInicio' => date("Y-m-d 00:00:00", strtotime('-7 day', strtotime($hoje))),
            'DataFim' => date("Y-m-d 23:59:00", strtotime($hoje)),
            'TIPO' => 0,
            'LOAN' => "'DS/280890'",
            'ESTADO' => "'" . $ids_estados . "'",
            'produtos_geral_busca' => "'" . $staticData['lista_produtos']->pluck('Metodologia')->implode(',') . "'",
            'formaspagamento_geral' => "'" . $staticData['lista_das_formaspagamento']->pluck('FormaPago')->implode(',') . "'"
        ];

        $tipoDeBusca = $request->tipo;

        if ($tipoDeBusca == 1) {
            $defaults['DataInicio'] = date("Y-m-d 00:00:00", strtotime($request->data_inicio));
            $defaults['DataFim'] = date("Y-m-d 23:59:00", strtotime($request->data_fim));
            $defaults['TIPO'] = $tipoDeBusca;

            if ($request->agenciaconsulta && $request->agenciaconsulta != 'T') {
                $defaults['Bases'] = "'" . $request->agenciaconsulta . "'";
            }

            if ($request->estadoconsulta && $request->estadoconsulta != 28) {
                $defaults['ESTADO'] = "'" . $request->estadoconsulta . "'";
            }
        } elseif ($tipoDeBusca == 3) {
            $defaults['LOAN'] = "'" . $request->loan . "'";
            $defaults['TIPO'] = $tipoDeBusca;
        } elseif ($tipoDeBusca == 4) {
            $defaults['DataInicio'] = date("Y-m-d 00:00:00", strtotime($request->data_inicio_imput));
            $defaults['DataFim'] = date("Y-m-d 23:59:00", strtotime($request->data_fim_imput));
            $defaults['TIPO'] = $tipoDeBusca;

            if ($request->estado_input && $request->estado_input != '28') {
                $defaults['ESTADO'] = $request->estado_input;
            }

            if ($request->agencia_imput && $request->agencia_imput != 'T') {
                $defaults['Bases'] = "'" . $request->agencia_imput . "'";
            }

            $this->processProductFilters($request, $defaults, $staticData);

            if ($request->forma_pagamento && $request->forma_pagamento != 'TP') {
                $defaults['formaspagamento_geral'] = "'" . $request->forma_pagamento . "'";
            }
        } elseif ($tipoDeBusca == 500000) {
            $defaults['TIPO'] = $tipoDeBusca;
        } elseif ($tipoDeBusca == 7000000) {
            $defaults['TIPO'] = $tipoDeBusca;
        }


        return $defaults;
    }

    protected function processProductFilters($request, &$defaults, $staticData)
    {
        $tipoProdutoPP = $request->filtrar_poupancas;
        $tipoProdutoPT = $request->filtrar_prestacoes;

        if ($tipoProdutoPT && !$tipoProdutoPP) {
            if ($request->produto_prestacao && $request->produto_prestacao != 'TL') {
                $defaults['produtos_geral_busca'] = "'" . $request->produto_prestacao . "'";
            } else {
                $produto_prestacoes_busca = collect($staticData['lista_produtos'])
                    ->where('TipoProduto', '=', 'L')
                    ->pluck('Metodologia')
                    ->implode(',');
                $defaults['produtos_geral_busca'] = "'" . $produto_prestacoes_busca . "'";
            }
        } elseif ($tipoProdutoPP && !$tipoProdutoPT) {
            if ($request->produto_poupanca && $request->produto_poupanca != 'TS') {
                $defaults['produtos_geral_busca'] = "'" . $request->produto_poupanca . "'";
            } else {
                $produto_poupancas_busca = collect($staticData['lista_produtos'])
                    ->where('TipoProduto', '=', 'S')
                    ->pluck('Metodologia')
                    ->implode(',');
                $defaults['produtos_geral_busca'] = "'" . $produto_poupancas_busca . "'";
            }
        }
    }

    protected function processComprovativosList($lista_comprovativo)
    {
        return collect($lista_comprovativo)->map(function ($item) {
            return [
                'id' => $item->id,
                'data' => $item->dataRegistoFomatada,
                'agencia' => $item->OfNombre,
                'basedelacamento' => $item->basedelacamento,
                'file' => $item->filecomprovativo,
                'usuario' => $item->UtNome,
                'lnr' => $item->BuDadoOrigem,
                'estado' => $item->estado,
                'color' => $item->color,
                'cliente' => $item->infoadicional,
                'observacao' => $item->observacao,
                'metodologia' => $item->PoAgrupado,
                'banco' => $item->BaSigla,
                'conta' => $item->ContaBacaria,
                'referencia' => $item->BuReferencia,
                'voucher' => $item->voucher,
                'FormaPagoN' => $item->FormaPagoN,
                'descricao' => $item->descricao,
                'operadordcf' => $item->operadordcf,
                'datareconciliacao' => $item->datareconciliacao,
                'montante' => $item->BuMontante,
                'CiFecha' => $item->CiFecha,
                'estado_id' => $item->idestado,
                'OfIdentificador' => $item->OfIdentificador,
                'BuMontante' => $item->BuMontante
            ];
        });
    }

    protected function calculateTotals($lista_comprovativo)
    {
        $collection = collect($lista_comprovativo);

        return [
            'montante_total' => $collection->where('TtCodigo', 'L04')->sum('BuMontante'),
            'montante_poupanca' => $collection->where('TtCodigo', 'S01')->sum('BuMontante'),
            'totalMontanteRegistado' => $collection->where('TtCodigo', '=', 'L04')->where('idestado', 1)->sum('BuMontante'),
            'totalMontantePoupancaRegistado' => $collection->where('TtCodigo', '=', 'S01')->where('idestado', 1)->sum('BuMontante'),
            'totalMontanteReflete' => $collection->where('TtCodigo', '=', 'L04')->where('idestado', 8)->sum('BuMontante'),
            'totalMontantePoupancaReflete' => $collection->where('TtCodigo', '=', 'S01')->where('idestado', 8)->sum('BuMontante'),
            'totalMontanteInregulares' => $collection->where('TtCodigo', '=', 'L04')->whereNotIn('idestado', [1, 8])->sum('BuMontante'),
            'totalMontantePoupancaInregulares' => $collection->where('TtCodigo', '=', 'S01')->whereNotIn('idestado', [1, 8])->sum('BuMontante'),
            'data_inicio_formatada' => Carbon::parse($collection->max('CiFecha'))->format('d/m/Y'),
            'data_fim_formatada' => Carbon::parse($collection->min('CiFecha'))->format('d/m/Y')
        ];
    }

    protected function getFilterParams($request)
    {
        return [
            'search' => $request->input('search_input', ''),
            'lnr' => $request->input('lnr_imput', ''),
            'estado' => $request->input('estado_input', 28),
            'agencia' => $request->input('agencia_imput', 'T'),
            'data_inicio' => $request->input('data_inicio_imput', ''),
            'data_fim' => $request->input('data_fim_imput', ''),
            'filtrar_prestacoes' => (bool) $request->input('filtrar_prestacoes', true),
            'filtrar_poupancas' => (bool) $request->input('filtrar_poupancas', true),
            'formaPagamento' => $request->input('forma_pagamento', 'TP')
        ];
    }

    public function validarComprovativo(Request $request)
    {
        $authenticatedUser = Auth::user();
        $hoje = date('Y-m-d H:i:s');

        // Verificação otimizada de duplicação
        if (
            CpvtReconciliacaoModel::where('idcomprovativo', $request->id)
                ->where('idestado', $request->estado)
                ->exists()
        ) {
            return back()->with('error', 'Ups!, não foi possível reconciliar o comprovativo, duplicação de estado');
        }

        try {
            DB::beginTransaction();

            $insert = CpvtReconciliacaoModel::create([
                'datareconciliacao' => $hoje,
                'CodigoConta' => $request->conta,
                'voucher' => $request->voucher,
                'descricao' => $request->descricao,
                'observacao' => $request->observacao,
                'idcomprovativo' => $request->id,
                'UtCodigo' => $authenticatedUser->UtCodigo,
                'idestado' => $request->estado
            ]);

            if ($insert) {
                ComprovativoModel::where('id', $request->id)->update([
                    'idestado' => $request->estado,
                    'BuReferencia' => $request->voucher,
                    'BaCodigo' => $request->banco
                ]);

                DB::commit();
                return redirect()->back()->with('success', 'Comprovativo reconciliado com sucesso!');
            }

            DB::rollBack();
            return back()->with('error', 'Ups!, não foi possível reconciliar o comprovativo');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro durante a reconciliação: ' . $e->getMessage());
        }
    }

    public function listarEstadosReconciliacao()
    {
        return response()->json(
            Cache::remember('estados_dcf', 3600, function () {
                return EstadosModel::getEstadosDCF('DCF');
            })
        );
    }

    public function listarDetalhesComprovativosDCF(Request $request)
    {
        if (!$request->idComprovativo || !is_numeric($request->idComprovativo)) {
            return response()->json([], 400);
        }

        return response()->json(
            CpvtReconciliacaoModel::getComprovativosDCFDetalhe($request->idComprovativo)
        );
    }

    public function listarDetalhesComprovativosDCF02(Request $request)
    {
        try {
            return response()->json(
                Cache::remember(
                    "operacoes_reconciliacao_{$request->idComprovativo}",
                    3600,
                    function () use ($request) {
                        return CpvtReconciliacaoModel::getComprovativosDCFDetalhe($request->idComprovativo);
                    }
                )
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar operações',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
