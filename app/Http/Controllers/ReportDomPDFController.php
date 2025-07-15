<?php

namespace App\Http\Controllers;

use App\Models\RecuperacaoModel;
use App\Models\RecuperadorModel;
use App\Models\TKxAgenciaModel;
use App\Models\TKxBancoModel;
use App\Models\TKxExtratoModel;
use App\Models\TKxUsUtilizadorModel;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportDomPDFController extends Controller
{
    public function emitirRelatorioCalculoDesembolso($id)
    {
        $Dados_extrato = TKxExtratoModel::getDadosReport($id);
        $authenticatedUser = Auth::user();
        $IMPRENSSO = $authenticatedUser->UtNome;
        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $authenticatedUser->UtAgencia)->first();
        $hoje = date('d-m-Y');
        $bancos = TKxBancoModel::all();

        $data = [
            //'title' => 'Welcome to ItSolutionStuff.com',
            'date' => date('d/m/Y'),
            'Dados_extrato' => $Dados_extrato,
            'IMPRENSSO' => $IMPRENSSO,
            'AGENCIA' => $resultagencia_user->OfNombre,
            'bancos' => $bancos
        ];

        $pdf = PDF::loadView('reports.reportCalculoDesembolso', $data)->setOption(['dpi' => 100, 'defaultFont' => 'sans-serif']);



        return $pdf->stream();//->download('CalculoDesembolso.pdf');
    }
    public static function emitirRelatorioFechoDiario()
    {
        $authenticatedUser = Auth::user();
        $IMPRENSSO = $authenticatedUser->UtNome;

        $utilizador_agencia = TKxAgenciaModel::where('OfCodigo', '=', $authenticatedUser->UtAgencia)->first();


        $BasesOperacao = explode(',', $authenticatedUser->BasesOperacao);

        $count_range = count($BasesOperacao);
        $fimVirgula = $count_range - 1;
        $ConsultaBases = "";

        $dataFecho = $authenticatedUser->DataFecho;

        $dataFecho = date("Y-m-d", strtotime($dataFecho));
        $hoje = date('Y-m-d');

        $dataActual = date("Y-m-d", strtotime($hoje));
        $sistema_aberto = true;

        if ($dataFecho == $dataActual) {
            $sistema_aberto = false;
        } else {
            $sistema_aberto = true;
        }

        for ($i = 0; $i < $count_range; $i++) {

            if ($fimVirgula == $i) {
                $ConsultaBases .= "'" . $BasesOperacao[$i] . "'";
            } else {
                $ConsultaBases .= "'" . $BasesOperacao[$i] . "',";
            }
        }

        $hoje = date('Y-m-d');
        $agencia = session('Agencia');

        $resumo_extrato = TKxUsUtilizadorModel::getResumoExtratos($ConsultaBases, $hoje);
        $resumo_comprovativos = TKxUsUtilizadorModel::getResumoComprovativos($ConsultaBases, $hoje);



        //  $pdf = PDF::loadView('kixiutilitario-web.reportFechoDiario', $data)->setOption(['dpi' => 100, 'defaultFont' => 'sans-serif']);


        //  return $pdf->stream(); //->download('CalculoDesembolso.pdf');
    }

    public function gerarRelatorioRecuperadoresPdf(Request $request)
    {

        $authenticatedUser = Auth::user();
        $IMPRENSSO = $authenticatedUser->UtNome;
        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $authenticatedUser->UtAgencia)->first();

        $sigla_agencia_base = $resultagencia_user->OfIdentificador;



        $ids_estados = $authenticatedUser->rec_viewestados;

        $Bases = $resultagencia_user->BasesOperacao;
        $ESTADO = $ids_estados;




        $agencia = $request->agencia;
        $estado = $request->estado;
        $recuperador = $request->recuperador;
        $data_inicio = $request->data_inicio;
        $data_fim = $request->data_fim;
        $listar_recuperador = RecuperadorModel::getRecuperadores($sigla_agencia_base);
        $RECUPERADORES = collect($listar_recuperador)->pluck('id')->implode(',');

        /*$filtros = [
            'recuperador' => $request->recuperador,
            'agencia' => $request->agencia,
            'estado' => $request->estado,
            'data_inicio' => $request->data_inicio,
            'data_fim' => $request->data_fim
        ];*/

        /* $recuperacoes = RecuperacaoModel::query()
             ->when($filtros['recuperador'], fn($q) => $q->whereIn('id_recuperador', $recuperadorArray))
             ->when($filtros['agencia'], fn($q) => $q->whereIn('BaseOperacao', $agenciaArray ))
             ->when($filtros['estado'], fn($q) => $q->whereIn('id_estado', $estadoArray))
             ->when($filtros['data_inicio'], fn($q) => $q->whereDate('CiFecha', '>=', $filtros['data_inicio']))
             ->when($filtros['data_fim'], fn($q) => $q->whereDate('CiFecha', '<=', $filtros['data_fim']))
             ->get();*/
        if ($request->estado == '28') {
            $estado = $ESTADO;

            // dd($request->estado_input);
        }
        if ($agencia == 'T') {
            $agencia = $Bases;
        }
        if ($recuperador == 'TR') {
            $recuperador = $RECUPERADORES;
        }



        $Dados_Recuperador = RecuperacaoModel::getRecuperacoes(0, 4, $estado, $agencia, $data_inicio, $data_fim, 'DS/02808', $recuperador);
        $RECUPERADORES = collect($Dados_Recuperador)->pluck('id_recuperador')->implode(',');
        $RECUPERADORES = explode(',', $RECUPERADORES);

        $listar_recuperador_com_recuperacao = RecuperadorModel::whereIn('id', $RECUPERADORES)->get();
//dd( $listar_recuperador_com_recuperacao );
        $dataFormatadaIni = Carbon::parse($request->data_inicio)->format('d-m-Y');
        $dataFormatadaFim = Carbon::parse($request->data_fim)->format('d-m-Y');

        $data = [
            //'title' => 'Welcome to ItSolutionStuff.com',
            'date' => date('d/m/Y'),
            'Dados_Recuperador' => $Dados_Recuperador,
            'IMPRENSSO' => $IMPRENSSO,
            'AGENCIA' => $resultagencia_user->OfNombre,
            'recuperadores' => $listar_recuperador_com_recuperacao,
            'data_inicio' => $dataFormatadaIni,
            'data_fim' => $dataFormatadaFim
        ];


        /*$pdf = PDF::loadView('recuperacoes.pdf', [
            'recuperacoes' => $recuperacoes,
            'filtros' => $filtros
        ]);

        return $pdf->download('relatorio-recuperacoes-' . now()->format('Y-m-d') . '.pdf');



        $pdf = PDF::loadView('reports.reportRecuperacoesRecuperador', [
            'recuperacoes' => $recuperacoes,
            'filtros' => $filtros
        ]);*/

        $pdf = PDF::loadView('reports.reportRecuperacoesRecuperador', $data)->setOption(['dpi' => 100, 'defaultFont' => 'sans-serif']);

        return $pdf->stream();


    }
}
