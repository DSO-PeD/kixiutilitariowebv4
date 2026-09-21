<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TblRDprintDadosEstatisticosModel extends Model
{
    protected $table = 'tblRDprintDadosEstatisticos';
    protected $primaryKey = 'Id';

    public $timestamps = false;

    protected $fillable = [
        'Id',
        'DataReferencia',
        'TipoAgrupamento',
        'NivelEscopo',
        'Ordem',
        'Homens_Qtd',
        'Mulheres_Qtd',
        'TempoSolicitacaoAprovacaoDias',
        'TempoAprovacaoDesembolsoDias',
        'TempoAtendimentoTotalDias',
        'MediaDesembolsoOficial',
        'MediaCreditosOficial',
        'MediaDesembolsoCredito',
        'MediaPrazoMeses',
        'MediaPrestacao',
        'MediaReembolso',
        'MediaDivida',
        'TaxaReembolsoPct',
        'TaxaCumprimentoPct',
        'TaxaRecuperacaoPct',
        'DataCriacao',
        'Activo'
    ];

    public static function getPorDataReferencia($dataReferencia)
    {
        return DB::table('tblRDprintDadosEstatisticos')
            ->where('DataReferencia', $dataReferencia)
            ->where('Activo', 1)
            ->orderBy('Ordem', 'asc')
            ->get();
    }

    public static function salvarOuAtualizarDados(array $dados)
    {
        return DB::table('tblRDprintDadosEstatisticos')->updateOrInsert(
            [
                'DataReferencia'  => $dados['DataReferencia'],
                'TipoAgrupamento' => $dados['TipoAgrupamento'],
                'NivelEscopo'     => $dados['NivelEscopo']
            ],
            $dados
        );
    }

    public static function eliminarPorDataReferencia($dataReferencia)
    {
        return DB::table('tblRDprintDadosEstatisticos')
            ->where('DataReferencia', $dataReferencia)
            ->delete();
    }
}
