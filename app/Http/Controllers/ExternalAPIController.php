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

        $query = TKxExtratoModel::whereDate('DataDesembolso', '>=', $dataI)
                                    ->whereDate('DataDesembolso', '<=', $dataF)
                                    ->select(
                                        'UtCodigo',
                                        'DataDesembolso as CiFecha',
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

        $query = DB::table('comprovativos as c')
                    ->leftjoin('tkxextrato as ext', 'ext.Lnr','=','c.BuDadoOrigem')
                    ->whereDate('c.CiFecha', '>=', $dataI)
                    ->whereDate('c.CiFecha', '<=', $dataF)
                    ->where('c.idestado', 8)
                    ->whereNotIn('c.PoCodigo', ['S00','S02','S03','S06','S08'])
                    ->select(
                        'c.UtCodigo',
                        'c.BuDadoOrigem',
                        'c.PoCodigo',
                        'c.BuMontante',
                        DB::raw('IFNULL(c.Capital, 0) as CAPI'),
                        DB::raw('IFNULL(c.Juros, 0) as JURO'),
                        'c.CiFecha',
                        'c.infoadicional',
                        DB::raw('IFNULL(ext.Bilhete, 999999999) as Bilhete'),
                    );

        if ($limite !== null) {
            $query->limit((int) $limite);
        }

        $comprovativos = $query->get();

        return response()->json($comprovativos);
    }
}
