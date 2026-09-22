<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TblRDPrintDesembolsosReembolsos12MesesModel extends Model
{
    protected $table = 'tblRDprintDesembolsosReembolsos12meses';
    protected $primaryKey = 'Id';

    public $timestamps = false;

    protected $fillable = [
        'Id',
        'DataReferencia',
        'NomeDoMes',
        'DesembolsoValor',
        'ReembolsoValor',
        'DataCriacao',
        'Activo'
    ];

    /**
     * Buscar histórico de 12 meses por Data de Referência
     */
    public static function getPorDataReferencia($dataReferencia)
    {
        return DB::table('tblrdprintdesembolsosreembolsos12meses')
            ->where('DataReferencia', $dataReferencia)
            ->where('Activo', 1)
            ->orderBy('Id', 'asc')
            ->get();
    }

    /**
     * Salvar ou Atualizar registros de 12 meses (Upsert)
     */
    public static function salvarOuAtualizarDados(array $dados)
    {
        return DB::table('tblrdprintdesembolsosreembolsos12meses')->updateOrInsert(
            [
                'DataReferencia' => $dados['DataReferencia'],
                'NomeDoMes'      => $dados['NomeDoMes']
            ],
            $dados
        );
    }

    /**
     * Eliminar registros por Data de Referência
     */
    public static function eliminarPorDataReferencia($dataReferencia)
    {
        return DB::table('tblrdprintdesembolsosreembolsos12meses')
            ->where('DataReferencia', $dataReferencia)
            ->delete();
    }
}
