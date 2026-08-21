<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TKxExtratoModel;
use App\Models\ComprovativoModel;
use Illuminate\Support\Facades\DB;

class ExternalAPIController extends Controller
{
    public function getDesembolsos(Request $request)
    {
        $dataI = $request->input('dataI');
        $dataF = $request->input('dataF');
        $limite = $request->input('limite'); 

        $query = TKxExtratoModel::whereDate('CiFecha', '>=', $dataI)
                                    ->whereDate('CiFecha', '<=', $dataF)
                                    ->select(
                                        'UtCodigo',
                                        'CiFecha',
                                        'Lnr',
                                        'Cliente',
                                        'TXAProcePercentaValor',
                                        'ValorIVATaxaProcessamento',
                                        'TXAImprePercentaValor',
                                        'ValorIVATaxaImprevisto',
                                        'TXAProcePercentaValorAnte',
                                        'ValorIVATaxaProcessamentoAnte',
                                        'BaseOperacao',
                                        DB::raw('IFNULL(Bilhete, 999999999) as Bilhete'),
                                    );

        if ($limite !== null) {
            $query->limit((int) $limite);
        }

        $desembolsos = $query->get();

        return response()->json($desembolsos);
    }
    
    public function getComprovativos(Request $request)
    {
        $dataI = $request->input('dataI');
        $dataF = $request->input('dataF');
        $limite = $request->input('limite'); 

        $query = ComprovativoModel::whereDate('CiFecha', '>=', $dataI)
                                    ->whereDate('CiFecha', '<=', $dataF)
                                    ->where('idestado', 8)
                                    ->whereNotIn('PoCodigo', ['S00','S02','S03','S06','S08'])
                                    ->select(
                                        'UtCodigo',
                                        'BuDadoOrigem',
                                        'PoCodigo',
                                        'BuMontante',
                                        DB::raw('IFNULL(Capital, 0) as CAPI'),
                                        DB::raw('IFNULL(Juros, 0) as JURO'),
                                        'CiFecha',
                                        'infoadicional'
                                    );

        if ($limite !== null) {
            $query->limit((int) $limite);
        }

        $comprovativos = $query->get();

        return response()->json($comprovativos);
    }
}
