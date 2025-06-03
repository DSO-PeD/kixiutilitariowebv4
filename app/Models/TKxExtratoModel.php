<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TKxExtratoModel extends Model
{
    protected $table = 'tkxextrato';

    protected $fillable = [
        'Num',
        'UtCodigo',
        'CiFecha',
        'OficialCredito',
        'Lnr',
        'Cliente',
        'Produto',
        'ValorCreditoNoContrato',
        'PercColateral',
        'ValorDoColateral',
        'PercColateralDeduzido',
        'ValorDoColateralDeduzido',
        'ValorDoCredito',
        'ValorTotalCredito',
        'TaxaProcessamento',
        'TXAProcePercenta',
        'TXAProcePercentaValor',
        'ValorIVATaxaProcessamento',
        'TaxaProcessamentoAnte',
        'TXAProcePercentaAnte',
        'TXAProcePercentaValorAnte',
        'ValorIVATaxaProcessamentoAnte' ,
        'TXAProceAnteBuReferencia',
        'TXAProceAnteBuBanco',
        'TXAProceAnteBuNumeroConta',
        'TXAProceAnteBuMontante',
        'TXAProceAnteBuData',
        'TaxaImprevisto',
        'TXAImprePercenta',
        'TXAImprePercentaValor',
        'ValorIVATaxaImprevisto',
        'TXAImpreAnteBuReferencia',
        'TXAImpreBuBanco',
        'TXAImpreBuNumeroConta',
        'TXAImpreBuMontante',
        'TXAImpreBuData',
        'ValorAKZTaxaDeConfirmacao',
        'DataRetiradaTaxaDeConfirmacao',
        'ValorIVATaxaConfirmacao',
        'DescricaoActividadeEconomica',
        'CodigoAtividade',
        'Sector',
        'Magnitude',
        'RendaMensal',
        'ValorPrimeiraPrestacao',
        'ppe',
        'NecessidadesEspeciais',
        'Eliminado',
        'EliminadoPor',
        'BaseOperacao',
        'referenciapagamento',
        'RefPgtActivo'
    ];

    public static function getExtratosPorDataRegistro($Bases, $DataInicio, $DataFim, $NumeroRegistroTabela, $TIPO, $LOAN)
    {

        $extratos2 = DB::select("CALL PKxExtratoCalculoDesembolso(" . $Bases . ",'" . $DataInicio . "','" . $DataFim . "'," . $NumeroRegistroTabela . "," . $TIPO . "," . $LOAN . ")");



        return $extratos2;
    }


    public static function getDadosReport($numRegistro)
    {



        $DadosDoreport = DB::table('tkxextrato')
            ->where('Num', $numRegistro)
            ->get();

        return $DadosDoreport;
    }


    public static function setEliminarExtrato($id, $UtCodigo, $DataEliminacao, $MotivoEliminacao, $djascript, $loan)
    {
        $statusEliminacao = false;



        $insertEraser = TbldjascriptEraserModel::create(['script_recuperacao' => $djascript, 'UtCodigoEliminou' => $UtCodigo, 'DataEliminacao' => $DataEliminacao, 'Motivo' => $MotivoEliminacao]);

        if ($insertEraser) {
            $deleteCpvtv = DB::table('tkxextrato')->where('Num', $id)->delete();

            if ($deleteCpvtv) {
                $delete_eraser_excepcoes = DB::table('eraser_excepcoes')->where('Lnr', $loan)->delete();

                if ($delete_eraser_excepcoes) {
                    $statusEliminacao = true;
                }
            }
            $statusEliminacao = true;
        }




        return $statusEliminacao;
    }

    public static function verificarSeExtratoExiste($Lnr, $siglaBase)
    {

        $extrato_existe = false;

        $c = DB::table('tkxextrato')
            ->where('Lnr', '=', $Lnr)
            ->where('Eliminado', '=', 0)->count();



        if ($c > 0) {
            $extrato_existe = true;
        }

        if ($siglaBase == 'AC') {
            $extrato_existe = true;
        }

        return $extrato_existe;
    }

    public static function getNecesidadesGrupo()
    {

        return DB::table('tkxgrupones')->get();
    }
    public static function getNecesidadesTipo()
    {

        return DB::table('tkxtiposnes')->get();
    }
    public static function getNecesidadesTipoById($id)
    {

        return DB::table('tkxtiposnes')->where('codigotipones', '=', $id)->get();
    }
    public static function replaceD($valor)
    {


        return str_replace(",", ".", $valor);
    }
}
