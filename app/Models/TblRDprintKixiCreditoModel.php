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
        'TipoDireccao',
        'DirectorNome',
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
        'Activo',
        'TipoDireccao',
        'DirectorNome'
    ];

    /**
     * Buscar histórico consolidado filtrado por TipoDireccao ('KIX', 'DPP', 'DOP', 'OUT')
     */
    public static function getHistoricoPorDireccao(string $tipoDireccao = 'KIX')
    {
        return DB::table('tblRDprintKixiCredito')
            ->where('Activo', 1)
            ->where('TipoDireccao', $tipoDireccao)
            ->orderBy('DataReferencia', 'asc')
            ->get();
    }

    /**
     * Obter registro por Data de Referência e Direção
     */
    public static function getPorDataReferencia($dataReferencia, string $tipoDireccao = 'KIX')
    {
        return DB::table('tblRDprintKixiCredito')
            ->where('DataReferencia', $dataReferencia)
            ->where('TipoDireccao', $tipoDireccao)
            ->where('Activo', 1)
            ->first();
    }

    /**
     * Salvar ou Atualizar registros consolidados (Upsert por DataReferencia + TipoDireccao)
     */
    public static function salvarOuAtualizarDados(array $dados)
    {
        return DB::table('tblRDprintKixiCredito')->updateOrInsert(
            [
                'DataReferencia' => $dados['DataReferencia'],
                'TipoDireccao'   => $dados['TipoDireccao'] ?? 'KIX',
            ],
            $dados
        );
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
