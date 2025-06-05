<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ComprovativoModel extends Model
{

    protected $table = 'comprovativos';
    protected $fillable = [
        'id',
        'CiFecha',
        'UtCodigo',
        'BaCodigo',
        'TtCodigo',
        'FormaPago',
        'PoCodigo',
        'BuDadoOrigem',
        'BuReferencia',
        'BuMontante',
        'BuData',
        'BuContaBancaria',
        'Eliminado',
        'UtCodigoEliminou',
        'DataEliminacao',
        'Motivo',
        'BaseOperacao',
        'infoadicional',
        'filecomprovativo',
        'OfNombre',
        'basedelacamento',
        'telefonecliente',
        'observacao',
        'idestado',
        'estado',
        'color'
    ];

    public static function getComprovativos($Bases, $DataInicio, $DataFim, $NumeroRegistroTabela, $TIPO, $LOAN,$ESTADO)
    {
        $comprovativos2 = DB::select("CALL PKxComprovativosLoanSaving(" . $Bases . ",'".$DataInicio."','".$DataFim."'," . $NumeroRegistroTabela . "," . $TIPO . "," . $LOAN .",".$ESTADO. ")");


        return $comprovativos2;
    }



    public static function setAlteraDataRegisto($id, $DataNova)
    {
        $affectedeCifecha = DB::table('comprovativos')
            ->where('id', $id)
            ->update(['CiFecha' => $DataNova]);

        return $affectedeCifecha;
    }
    /*   public static function setEliminarComprovativo($id, $UtCodigo, $DataEliminacao, $MotivoEliminacao, $djascript, $loan)
       {


           $statusEliminacao = false;

           $insertEraser = Tbldjascript_eraser::create(['script_recuperacao' => $djascript, 'UtCodigoEliminou' => $UtCodigo, 'DataEliminacao' => $DataEliminacao, 'Motivo' => $MotivoEliminacao]);

           if ($insertEraser) {
               $deleteCpvtv = DB::table('comprovativos')->where('id', $id)->delete();

               if ($deleteCpvtv) {
                   $delete_eraser_excepcoes = DB::table('eraser_excepcoes')->where('Lnr', $loan)->delete();

                   if ($delete_eraser_excepcoes) {
                       $statusEliminacao = true;
                   }
                   $statusEliminacao = true;
               }
           }








           return $statusEliminacao;
       }*/

    public static function verificarSeBorderouxExiste($BuReferencia, $BuData, $BaCodigo, $Eliminado)
    {

        $comprovativo_existe = false;

        $c = DB::table('comprovativos')
            ->where('BuReferencia', '=', $BuReferencia)
            ->whereNotNull('BuReferencia')
            //->where('BuData', '=', $BuData)
            //->where('BaCodigo', '=', $BaCodigo)
            ->where('Eliminado', '=', $Eliminado)
            ->first();
        return $c;
    }
    public static function setEliminarComprovativo($id, $UtCodigo, $DataEliminacao, $MotivoEliminacao, $djascript, $loan)
    {


        $statusEliminacao = false;

        $insertEraser = TbldjascriptEraserModel::create(['script_recuperacao' => $djascript, 'UtCodigoEliminou' => $UtCodigo, 'DataEliminacao' => $DataEliminacao, 'Motivo' => $MotivoEliminacao]);

        if ($insertEraser) {
            $deleteCpvtv = DB::table('comprovativos')->where('id', $id)->delete();

            if ($deleteCpvtv) {
                $delete_eraser_excepcoes = DB::table('eraser_excepcoes')->where('Lnr', $loan)->delete();

                if ($delete_eraser_excepcoes) {
                    $statusEliminacao = true;
                }
                $statusEliminacao = true;
            }
        }








        return $statusEliminacao;
    }
}
