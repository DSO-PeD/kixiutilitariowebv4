<?php

namespace App\Http\Controllers;

use App\Models\TKxAgenciaModel;
use App\Models\TKxBancoModel;
use App\Models\TKxExtratoModel;
use App\Models\TKxUsUtilizadorModel;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
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
        ;


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

        $IMPRENSSO = session('LoggedUser');
        $data = [

            'date' => date('d/m/Y'),
            'agencia' => $agencia,
            'resumo_extrato' => $resumo_extrato,
            'resumo_comprovativos' => $resumo_comprovativos,
            'IMPRENSSO' => $IMPRENSSO
        ];

        $pdf = PDF::loadView('kixiutilitario-web.reportFechoDiario', $data)->setOption(['dpi' => 100, 'defaultFont' => 'sans-serif']);
        ;

        return $pdf->stream(); //->download('CalculoDesembolso.pdf');
    }
}
