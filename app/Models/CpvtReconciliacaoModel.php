<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CpvtReconciliacaoModel extends Model
{
    //

    protected $table = 'cpvtreconciliacao';
    protected $fillable = [
        'id',
        'datareconciliacao',
        'CodigoConta',
        'voucher',
        'vouchertransacao',
        'descricao',
        'observacao',
        'idcomprovativo',
        'UtCodigo',
        'idestado'
    ];


    public static function getComprovativosDCFDetalhe($idcomprovativo)
    {
        try {
            // Converter para inteiro para segurança
            $id = (int) $idcomprovativo;

            // Executar a procedure com parâmetro bindizado
            $resultados = DB::select(
                "CALL PKxListarDetalheDCF(?)",
                [$id]
            );

            // Converter resultados para array associativo
            return array_map(function ($item) {
                return (array) $item;
            }, $resultados);

        } catch (\Exception $e) {
           // \Log::error("Falha na procedure PKxListarDetalheDCF: " . $e->getMessage());
            return []; // Retorna array vazio em caso de erro
        }
    }
}
