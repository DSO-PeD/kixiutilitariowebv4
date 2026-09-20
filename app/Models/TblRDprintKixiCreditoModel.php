<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TblRDprintKixiCreditoModel extends Model
{
    protected $table = 'tblRDprintKixiCredito';
    protected $primaryKey = 'Id';

    public $timestamps = false;

    protected $fillable = [
        'Id',
        'DataReferencia',
        'BalancoValor',
        'CreditosQuantidade',
        'CreditosNovos',
        'CreditosNovos_Mais',
        'CreditosNovos_Menos',
        'VariacaoCreditosNovos',
        'TaxaJuros1',
        'TaxaJuros2',
        'CreditosAteKz2000_Qtd',
        'CreditosAteKz2000_Valor',
        'ClientesQuantidade',
        'ClientesNovos',
        'ClientesNovos_Mais',
        'ClientesNovos_Menos',
        'VariacaoClientesNovos',
        'ClientesAteKz2000',
        'NPLPercentual',
        'NPLValor',
        'NPL_Mais',
        'NPL_Menos',
        'VariacaoNPL',
        'PAR1Percentual',
        'PAR1Valor',
        'PAR1_Mais',
        'PAR1_Menos',
        'VariacaoPAR1',
        'PAR30Percentual',
        'PAR30Valor',
        'PAR30_Mais',
        'PAR30_Menos',
        'VariacaoPAR30',
        'ProvisaoValor',
        'ProvisaoCoberturaPerc',
        'ProvisaoDiasAtraso',
        'Provisao_Mais',
        'Provisao_Menos',
        'VariacaoProvisao',
        'TaxaReembolsoPercentual',
        'TaxaReembolso_Mais',
        'TaxaReembolso_Menos',
        'VariacaoTaxaReembolso',
        'TaxaCumprimentoPercentual',
        'TaxaCumprimento_Mais',
        'TaxaCumprimento_Menos',
        'VariacaoTaxaCumprimento',
        'TaxaRecuperacaoPercentual',
        'TaxaRecuperacao_Mais',
        'TaxaRecuperacao_Menos',
        'VariacaoTaxaRecuperacao',
        'Desembolsos',
        'Reembolso',
        'VariacaoPercentual',
        'DataCriacao',
        'Activo'
    ];

    /**
     * Obter registro por Data de Referência
     */
    public static function getPorDataReferencia($dataReferencia)
    {
        return DB::table('tblRDprintKixiCredito')
            ->where('DataReferencia', $dataReferencia)
            ->where('Activo', 1)
            ->first();
    }

    /**
     * Salvar ou Atualizar registros consolidados (Upsert)
     */
    public static function salvarOuAtualizarDados(array $dados)
    {
        return DB::table('tblRDprintKixiCredito')->updateOrInsert(
            ['DataReferencia' => $dados['DataReferencia']],
            $dados
        );
    }

    /**
     * Buscar todos os relatórios consolidados ordenados por data
     */
    public static function getHistoricoConsolidado()
    {
        return DB::table('tblRDprintKixiCredito')
            ->where('Activo', 1)
            ->orderBy('DataReferencia', 'desc')
            ->get();
    }

    /**
     * Eliminar registro por ID
     */
    public static function eliminarPorId($id)
    {
        return DB::table('tblRDprintKixiCredito')
            ->where('Id', $id)
            ->delete();
    }
}
