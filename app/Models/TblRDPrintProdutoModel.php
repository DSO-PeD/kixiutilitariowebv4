<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TblRDPrintProdutoModel extends Model
{
    protected $table = 'tblRDprintProduto';
    protected $primaryKey = 'Id';

    public $timestamps = false;

    protected $fillable = [
        'Id',
        'DataReferencia',
        'TipoAgrupamento',
        'NomeProduto',
        'TipoDireccao',
        'DirectorNome',
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

    public static function getPorDataReferencia($dataReferencia)
    {
        return DB::table('tblrdprintproduto')
            ->where('DataReferencia', $dataReferencia)
            ->where('Activo', 1)
            ->get();
    }

    public static function salvarOuAtualizarDados(array $dados)
    {
        return DB::table('tblrdprintproduto')->updateOrInsert(
            [
                'DataReferencia' => $dados['DataReferencia'],
                'NomeProduto'    => $dados['NomeProduto']
            ],
            $dados
        );
    }

    public static function eliminarPorDataReferencia($dataReferencia)
    {
        return DB::table('tblrdprintproduto')
            ->where('DataReferencia', $dataReferencia)
            ->delete();
    }
}
