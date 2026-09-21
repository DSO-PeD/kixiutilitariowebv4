<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TblRDprintRegionalModel extends Model
{
    //

    protected $table = 'tblRDprintRegional';
    protected $primaryKey = 'Id';

    public $timestamps = false;

    protected $fillable = [
        'Id',
        'DataReferencia',
        'TipoAgrupamento',
        'NomeLocal',
        'PesoPercentual',
        'IsTotalizador',
        'OA_Qtd',
        'PA_Qtd',
        'BalancoValor',
        'CreditosTotal_Qtd',
        'CreditosNovos_Qtd',
        'ClientesTotal_Qtd',
        'ClientesNovos_Qtd',
        'PAR1_Valor',
        'PAR1_Percentual',
        'PAR30_Valor',
        'PAR30_Percentual',
        'DesembolsoValor',
        'TempoAtendimentodias',
        'ReembolsoValor',
        'ProvisaoValor',
        'ProvisaoClasseRisco',
        'GestaoValor',
        'GestaoSufixo',
        'DataCriacao',
        'Activo'
    ];

    /**
     * Buscar dados de agências por Data de Referência
     */
    public static function getPorDataReferencia($dataReferencia)
    {
        return DB::table('tblRDprintRegional')
            ->where('DataReferencia', $dataReferencia)
            ->where('Activo', 1)
            ->get();
    }

    /**
     * Salvar ou Atualizar registros de agências (Upsert)
     */
    public static function salvarOuAtualizarDados(array $dados)
    {
        return DB::table('tblRDprintRegional')->updateOrInsert(
            [
                'DataReferencia' => $dados['DataReferencia'],
                'NomeLocal'      => $dados['NomeLocal']
            ],
            $dados
        );
    }

    /**
     * Eliminar registros por Data de Referência
     */
    public static function eliminarPorDataReferencia($dataReferencia)
    {
        return DB::table('tblRDprintRegional')
            ->where('DataReferencia', $dataReferencia)
            ->delete();
    }
}
