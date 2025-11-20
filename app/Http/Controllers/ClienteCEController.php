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

        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $authenticatedUser->UtAgencia)->first();




        $tipoDeBusca = $request->tipo;
        $tipoProdutoPP = $request->filtrar_poupancas;
        $tipoProdutoPT = $request->filtrar_prestacoes;



        $lista_produtos = TKxClProdutoModel::getProdutos();




        $NumeroRegistroTabela = $resultagencia_user->NumeroRegistroTabela;
        $dataFecho = $resultagencia_user->DataFecho;

        $dataFecho = date("Y-m-d", strtotime($dataFecho));
        $hoje = date('Y-m-d');

        $dataActual = date("Y-m-d", strtotime($hoje));


        $estados = EstadosModel::getEstadosDCF('RPGT');
        $ids_estados = $estados->pluck('id')->implode(',');

        $produto_poupancas_busca = collect($lista_produtos)->where('TipoProduto', '=', 'S');
        $produto_poupancas_busca = "'" . $produto_poupancas_busca->pluck('Metodologia')->implode(',') . "'";


        $produto_prestacoes_busca = collect($lista_produtos)->where('TipoProduto', '=', 'L');
        $produto_prestacoes_busca = "'" . $produto_prestacoes_busca->pluck('Metodologia')->implode(',') . "'";

        $produtos_geral_busca = "'" . $lista_produtos->pluck('Metodologia')->implode(',') . "'";



        $Bases = "'" . $resultagencia_user->BasesOperacao . "'";


        $ESTADO = "'" . $ids_estados . "'";
        $DataInicio = date("Y-m-d 00:00:00", strtotime('-7 day', strtotime($hoje)));
        $DataFim = date("Y-m-d 23:59:00", strtotime($hoje));


        $TIPO = 7371;
        $LOAN = "'DS/280890'";

        $BasesOperacao = explode(',', $resultagencia_user->BasesOperacao);



        if ($tipoDeBusca == 1) {
            $DataInicio = date("Y-m-d 00:00:00", strtotime($request->data_inicio));
            $DataFim = date("Y-m-d 23:59:00", strtotime($request->data_fim));
            $TIPO = $tipoDeBusca;

            //dd( $DataFim );
        }

        if ($tipoDeBusca == 3) {
            $LOAN = "'" . $request->loan . "'";
            $TIPO = 73713;
        }
        if ($tipoDeBusca == 4) {

            $DataInicio = date("Y-m-d 00:00:00", strtotime($request->data_inicio_imput));
            $DataFim = date("Y-m-d 23:59:00", strtotime($request->data_fim_imput));


            if ($request->agencia_imput !== 'T') {
                $Bases = "'" . $request->agencia_imput . "'";
            }



            $TIPO = 73714;
        }




        $lista_comprovativo = ComprovativoModel::getComprovativos($Bases, $DataInicio, $DataFim, $NumeroRegistroTabela, $TIPO, $LOAN, $ESTADO, $produtos_geral_busca, "'DJA'");





        $BasesOperacaoAgencias = TKxAgenciaModel::whereIn('OfIdentificador', $BasesOperacao)->get();
        $total = sizeof($lista_comprovativo);


        collect($lista_comprovativo)->max('created_at');
        collect($lista_comprovativo)->min('created_at');

        $DataInicioFormatada = Carbon::parse($DataInicio)->format('d/m/Y');
        $DataFimFormatada = Carbon::parse($DataFim)->format('d/m/Y');




        $TipoComprovativo = [
            'G' => 'G/',
            'I' => 'I/'
        ];
        // dd($lista_comprovativo );
        $comprovativos_list = collect($lista_comprovativo)->map(function ($item) {




            return [
                'id' => $item->id,
                'data' => $item->dataRegistoFomatada,
                'agencia' => $item->OfNombre,
                'basedelacamento' => $item->basedelacamento,
                'usuario' => $item->UtNome,
                'lnr' => $item->BuDadoOrigem,
                'inicio' => $item->inicio,
                'fim' => $item->fim,
                'estado' => $item->estado,
                'color' => $item->color,
                'idestado' => $item->idestado,
                'cliente' => $item->nomecliente,
                'telefone' => $item->telefone,
                'metodologia' => $item->PoAgrupado,
                'referencia' => $item->referencia,
                'montante' => $item->montante,
                'montantepago' => $item->montantepago,
                'TipoProduto' => $item->TipoProduto,
                // Mantenha todos os campos necessários para filtros client-side
                'CiFecha' => $item->created_at, // Para filtro por data
                'OfIdentificador' => $item->OfIdentificador, // Para filtro por agência


            ];
        });





        return Inertia::render('ClienteCorp', [
            'lista_comprovativo' => $comprovativos_list,
            // 'comprovativos' => $paginado,
            'filters' => [
                'search' => $request->input('search_input', ''),
                'lnr' => $request->input('lnr_imput', ''),
                'estado' => $request->input('estado_input', 28), // Valor padrão 28 (Todos)
                'agencia' => $request->input('agencia_imput', default: 'T'), // Valor padrão 'T' (Todas)
                'formaPagamento' => $request->input('forma_pagamento', 'TP'), // Valor padrão 'T' (Todas)
                'produtoPrestacao' => $request->input('produto_prestacao', 'TL'),
                'produtoPoupanca' => $request->input('produto_poupanca', 'TS'),
                'data_inicio' => $request->input('data_inicio_imput', ''),
                'data_fim' => $request->input('data_fim_imput', ''),
                'filtrar_prestacoes' => (bool) $request->input('filtrar_prestacoes', true),
                'filtrar_poupancas' => (bool) $request->input('filtrar_poupancas', true),
            ],
            'page' => (int) $request->input('page', 1),
            'bases' => $BasesOperacaoAgencias,
            'produtos' => $lista_produtos,

            'tipocomprovativos' => $TipoComprovativo,
            'estados' => $estados,
            //  'lista_comprovativo' => $lista_comprovativo,
            'total' => $total,

            'dataInicioPeriodo' => $DataFimFormatada,
            'dataFimPeriodo' => $DataInicioFormatada

        ]);



    }
}
