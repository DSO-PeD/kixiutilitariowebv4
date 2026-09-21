<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TblRDPrintCarteiraWriteOffModel extends Model
{
    protected $table = 'tblRDPrintCarteiraWriteOff';
    protected $primaryKey = 'Id';

    public $timestamps = false;

    protected $fillable = [
        'Id',
        'DataReferencia',
        'TipoAgrupamento',
        'Orden',
        'EtiquetaPeriodoWO',
        'BalancoInicial',
        'BalancoFinal',
        'RecuperadoAcumuladoAnterior',
        'RecuperadoMes1',
        'RecuperadoMes2',
        'RecuperadoMes3',
        'RecuperadoMes4',
        'RecuperadoMes5',
        'TotalRecuperado',
        'PctRecuperadoBalancoInicial',
        'PctKR',
        'DataCriacao',
        'Activo'
    ];

    public static function getPorDataReferencia($dataReferencia)
    {
        return DB::table('tblRDPrintCarteiraWriteOff')
            ->where('DataReferencia', $dataReferencia)
            ->where('Activo', 1)
            ->orderBy('Orden', 'asc')
            ->get();
    }

    public static function salvarOuAtualizarDados(array $dados)
    {
        return DB::table('tblRDPrintCarteiraWriteOff')->updateOrInsert(
            [
                'DataReferencia'    => $dados['DataReferencia'],
                'EtiquetaPeriodoWO' => $dados['EtiquetaPeriodoWO']
            ],
            $dados
        );
    }

    public static function eliminarPorDataReferencia($dataReferencia)
    {
        return DB::table('tblRDPrintCarteiraWriteOff')
            ->where('DataReferencia', $dataReferencia)
            ->delete();
    }
}
