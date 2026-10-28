<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TblRDPrintMovimentoPrestacaoModel extends Model
{
    protected $table = 'tblRDprintMovimentoPrestacao';
    protected $primaryKey = 'Id';

    public $timestamps = false;

    protected $fillable = [
        'Id',
        'DataReferencia',
        'TipoAgrupamento',
        'Orden',
        'PeriodoPrestacao',
        'CapIniValor',
        'CapDesValor',
        'CapReeValor',
        'CapFimValor',
        'JurIniValor',
        'JurDesValor',
        'JurReeValor',
        'JurFimValor',
        'TotalRecuperado',
        'PctRecuperado',
        'TxRecuperacao',
        'DataCriacao',
        'Activo'
    ];

    public static function getPorDataReferencia($dataReferencia)
    {
        return DB::table('tblRDprintMovimentoPrestacao')
            ->where('DataReferencia', $dataReferencia)
            ->where('Activo', 1)
            ->orderBy('Orden', 'asc')
            ->get();
    }

    public static function salvarOuAtualizarDados(array $dados)
    {
        return DB::table('tblRDprintMovimentoPrestacao')->updateOrInsert(
            [
                'DataReferencia'   => $dados['DataReferencia'],
                'PeriodoPrestacao' => $dados['PeriodoPrestacao']
            ],
            $dados
        );
    }

    public static function eliminarPorDataReferencia($dataReferencia)
    {
        return DB::table('tblRDprintMovimentoPrestacao')
            ->where('DataReferencia', $dataReferencia)
            ->delete();
    }
}
