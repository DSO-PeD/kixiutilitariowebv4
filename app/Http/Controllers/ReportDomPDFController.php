<?php

namespace App\Http\Controllers;

use App\Models\EstadosModel;
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
        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $authenticatedUser->UtAgencia)->first();
        $Bases = $resultagencia_user->BasesOperacao;
        $listar_estados = EstadosModel::getEstadosDCF('DOP'); // os IDs DOP tambem servem para DPP
        $ids_estados = collect($listar_estados)->pluck('id')->implode(',');

        $IMPRENSSO = $authenticatedUser->UtNome;
        $agencia = $request->agencia;
        $estado = $request->estado;
        $recuperador = $request->recuperador;
        $data_inicio = $request->data_inicio;
        $data_fim = $request->data_fim;
        $listar_recuperador = "";


        if ($agencia == 'T') {
            $agencia = $Bases;
        }
        if ($recuperador == 'TR') {

            $listar_recuperador = RecuperadorModel::getRecuperadores($resultagencia_user->BasesOperacao);

            $recuperador =  collect($listar_recuperador)->pluck('id')->implode(',');
        }
        if ($estado == '28') {

            $estado = $ids_estados;


        }






        $Dados_Recuperador = RecuperacaoModel::getRecuperacoes(0, 4, $estado, $agencia, $data_inicio, $data_fim, 'DS/02808', $recuperador);
        $RECUPERADORES = collect($Dados_Recuperador)->pluck('id_recuperador')->implode(',');
        $RECUPERADORES = explode(',', $RECUPERADORES);
        $listar_recuperador_com_recuperacao = RecuperadorModel::whereIn('id', $RECUPERADORES)->get();

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



        $pdf = PDF::loadView('reports.reportRecuperacoesRecuperador', $data)->setOption(['dpi' => 100, 'defaultFont' => 'sans-serif']);

        return $pdf->stream();


    }
}
