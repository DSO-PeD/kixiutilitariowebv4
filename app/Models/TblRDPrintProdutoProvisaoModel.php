<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TblRDPrintProdutoProvisaoModel extends Model
{
    protected $table = 'tblrdprintprodutoprovisao';
    protected $primaryKey = 'Id';

    public $timestamps = false;

    protected $fillable = [
        'Id',
        'DataReferencia',
        'TipoAgrupamento',
        'NomeProduto',
        'TipoDireccao',
        'DirectorNome',
        'PercentagemDado',
        'ProvisaoValorFechoAnoAnterior',
        'ProvisaoValorMesAnterior',
        'ProvisaoValorInferiorAnoActual',
        'ProvisaoValorAnoActual',
        'ProvisaoValorMesActual',
        'ProvisaoDiferencaMensal',
        'ProvisaoDiferencaAnual',
        'ProvisaoDiferencaMediaMensal',
        'PercentVariaAnual',
        'DataCriacao',
        'Activo'
    ];

    public static function getPorDataReferencia($dataReferencia)
    {
        return DB::table('tblrdprintprodutoprovisao')
            ->where('DataReferencia', $dataReferencia)
            ->where('Activo', 1)
            ->get();
    }

    public static function salvarOuAtualizarDados(array $dados)
    {
        return DB::table('tblrdprintprodutoprovisao')->updateOrInsert(
            [
                'DataReferencia' => $dados['DataReferencia'],
                'NomeProduto'    => $dados['NomeProduto']
            ],
            $dados
        );
    }

    public static function eliminarPorDataReferencia($dataReferencia)
    {
        return DB::table('tblrdprintprodutoprovisao')
            ->where('DataReferencia', $dataReferencia)
            ->delete();
    }
}
