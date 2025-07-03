<?php

namespace App\Http\Controllers;

use App\Models\CpvtReconciliacaoModel;
use App\Models\EstadosModel;
use Illuminate\Http\Request;
use App\Models\ComprovativoModel;
use App\Models\RecuperacaoModel;
use App\Models\TKxAgenciaModel;
use App\Models\TKxBancoContaModel;
use App\Models\TKxBancoModel;
use App\Models\TKxClProdutoModel;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class CpvtReconciliacaoController extends Controller
{

    public function viewComprovativosReconlicacao(Request $request)
    {


        $authenticatedUser = Auth::user();

        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $authenticatedUser->UtAgencia)->first();


        // $filtros = $request->only(['search', 'data_inicio', 'data_fim', 'estadoconsulta', 'agenciaconsulta']);



        $tipoDeBusca = $request->tipo;



        $NumeroRegistroTabela = $resultagencia_user->NumeroRegistroTabela;
        $dataFecho = $resultagencia_user->DataFecho;

        $dataFecho = date("Y-m-d", strtotime($dataFecho));
        $hoje = date('Ymd');
        $ConsultaBasesConsulta = "'" . $resultagencia_user->BasesOperacao . "'";
        $dataActual = date("Y-m-d", strtotime($hoje));
        $sistema_aberto = true;


        if ($dataFecho == $dataActual) {
            $sistema_aberto = false;
        } else {
            $sistema_aberto = true;
        }
        $estados = EstadosModel::getEstadosDCF('DCF');
        $ids_estados = $estados->pluck('id')->implode(',');

        $Bases = "'" . $resultagencia_user->BasesOperacao . "'";
        $ESTADO = "'" . $ids_estados . "'";

        $DataInicio = date("Y-m-d 00:00:00", strtotime('-7 day', strtotime($hoje)));
        $DataFim = date("Y-m-d 23:59:00", strtotime($hoje));
        $TIPO = 0;
        $LOAN = "'DS/280890'";

        $BasesOperacao = explode(',', $resultagencia_user->BasesOperacao);

        // COSULTAS PARA FILTRO

        if ($tipoDeBusca == 1) {

            $DataInicio = date("Y-m-d 00:00:00", strtotime($request->data_inicio));
            $DataFim = date("Y-m-d 23:59:00", strtotime($request->data_fim));
            $TIPO = $tipoDeBusca;

            if ($request->agenciaconsulta != 'T' || $request->agenciaconsulta != '') {
                $Bases = "'" . $request->agenciaconsulta . "'";
            }

            if ($request->estadoconsulta != 28 || $request->estadoconsulta != '') {
                $ESTADO = "'" . $request->estadoconsulta . "'";

            }





        }

        if ($tipoDeBusca == 3) {
            $LOAN = "'" . $request->loan . "'";
            $TIPO = $tipoDeBusca;

        }

        if ($tipoDeBusca == 4) {

            $DataInicio = date("Y-m-d 00:00:00", strtotime($request->data_inicio_imput));
            $DataFim = date("Y-m-d 23:59:00", strtotime($request->data_fim_imput));

            if ($request->estado_input !== '28') {
                $ESTADO = $request->estado_input;
                // dd($request->estado_input);
            }
            if ($request->agencia_imput !== 'T') {
                $Bases = "'" . $request->agencia_imput . "'";
            }

            $TIPO = $tipoDeBusca;
        }



        $lista_comprovativo = ComprovativoModel::getComprovativos($Bases, $DataInicio, $DataFim, $NumeroRegistroTabela, $TIPO, $LOAN, $ESTADO);
        $lista_produtos = TKxClProdutoModel::getProdutos();
        $lista_banco = TKxBancoModel::getBancos();
        $lista_bancos_contas = TKxBancoContaModel::getBancosContas();
        $BasesOperacaoAgencias = TKxAgenciaModel::whereIn('OfIdentificador', $BasesOperacao)->get();
        $total = sizeof($lista_comprovativo);


        $totalMontante = collect($lista_comprovativo)->where('TtCodigo', '=', 'L04')->sum('BuMontante');
        $totalMontantePoupanca = collect($lista_comprovativo)->where('TtCodigo', '=', 'S01')->sum('BuMontante');
        $DataInicio = collect($lista_comprovativo)->max('CiFecha');
        $DataFim = collect($lista_comprovativo)->min('CiFecha');

        $DataInicioFormatada = Carbon::parse($DataInicio)->format('d/m/Y');
        $DataFimFormatada = Carbon::parse($DataFim)->format('d/m/Y');


        $TipoComprovativo = [
            'G' => 'G/',
            'I' => 'I/'
        ];



        $comprovativos_list = collect($lista_comprovativo)->map(function ($item) {
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
                'descricao' => $item->descricao,
                'operadordcf' => $item->operadordcf,
                'datareconciliacao' => $item->datareconciliacao,
                'montante' => $item->BuMontante,
                // Mantenha todos os campos necessários para filtros client-side
                'CiFecha' => $item->CiFecha, // Para filtro por data
                'estado_id' => $item->idestado, // Para filtro por estado
                'OfIdentificador' => $item->OfIdentificador, // Para filtro por agência
                'BuMontante' => $item->BuMontante // Para cálculos
            ];
        });

        $NumeroPaginator = $NumeroRegistroTabela;
        // $paginado = $comprovativos_list->forPage($request->input('page', 1), $NumeroPaginator)->values();



        return Inertia::render('Reconciliacao', [
            'lista_comprovativo' => $comprovativos_list,
            // 'comprovativos' => $paginado,
            'filters' => [
                'search' => $request->input('search_input', ''),
                'lnr' => $request->input('lnr_imput', ''),
                'estado' => $request->input('estado_input', 28), // Valor padrão 28 (Todos)
                'agencia' => $request->input('agencia_imput', 'T'), // Valor padrão 'T' (Todas)
                'data_inicio' => $request->input('data_inicio_imput', ''),
                'data_fim' => $request->input('data_fim_imput', '')
            ],
            'page' => (int) $request->input('page', 1),
            'bases' => $BasesOperacaoAgencias,
            'produtos' => $lista_produtos,
            'bancos' => $lista_banco,
            'contas' => $lista_bancos_contas,
            'tipocomprovativos' => $TipoComprovativo,
            'estados' => $estados,
            'total' => $total,
            'montantetotal' => $totalMontante,
            'dataInicioPeriodo' => $DataInicioFormatada,
            'dataFimPeriodo' => $DataFimFormatada,
            'totalMontantePoupanca' => $totalMontantePoupanca

        ]);
    }

    public function validarComprovativo(Request $request)
    {
        $authenticatedUser = Auth::user();
        $hoje = date('Y-m-d H:i:s');

        // Verifica se já existe uma reconciliação para o comprovativo
        $reconciliacao = CpvtReconciliacaoModel::where('idcomprovativo', $request->id)->where('idestado', $request->estado)->first();

        if ($reconciliacao) {


            return back()->with('error', 'Ups!, não foi possível reconciliar o comprovativo, duplicação de estado');
        } else {
            // Cria um novo registro
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
                $comprovativo = ComprovativoModel::where('id', $request->id)->first();

                //Ao reconciliar, o novo voucher da contabilidade deve reflectir no comprovativo, bem como o banco
                $comprovativo->update([
                    'idestado' => $request->estado,
                    'BuReferencia' => $request->voucher,
                    'BaCodigo' => $request->banco
                ]);
                return redirect()->back()->with('success', 'Comprovativo reconciliado (criado) com sucesso!');
            } else {
                return back()->with('error', 'Ups!, não foi possível reconciliar o comprovativo');
            }

        }
    }

    public function listarEstadosReconciliacao()
    {

        $estados = EstadosModel::getEstadosDCF('DCF');
        return response()->json($estados);
    }



    public function listarDetalhesComprovativosDCF(Request $request)
    {
        // Validação mínima
        if (!$request->idComprovativo || !is_numeric($request->idComprovativo)) {
            return response()->json([], 400);
        }

        $dados = CpvtReconciliacaoModel::getComprovativosDCFDetalhe($request->idComprovativo);

        return response()->json($dados);

    }

    public function listarDetalhesComprovativosDCF02(Request $request)
    {
        try {


            // Cache com tempo de 1 hora (3600 segundos)
            $dados = Cache::remember(
                "operacoes_reconciliacao_{$request->idComprovativo}",
                3600,
                function () use ($request) {
                    return CpvtReconciliacaoModel::getComprovativosDCFDetalhe($request->idComprovativo);
                }
            );

            return response()->json($dados);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar operações',
                'message' => $e->getMessage()
            ], 500);
        }
    }




}
