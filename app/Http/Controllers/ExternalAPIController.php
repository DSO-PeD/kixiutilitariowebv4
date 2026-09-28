<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TKxExtratoModel;
use App\Models\ComprovativoModel;
use App\Models\TblRDPrintCarteiraWriteOffModel;
use App\Models\TblRDprintDadosEstatisticosModel;
use App\Models\TblRDPrintDesembolsosReembolsos12MesesModel;
use App\Models\TblRDprintKixiCreditoModel;
use App\Models\TblRDPrintMovimentoPrestacaoModel;
use App\Models\TblRDPrintProdutoModel;
use Illuminate\Support\Facades\DB;

class ExternalAPIController extends Controller
{
    public function getDesembolsos(Request $request)
    {
        $dataI = $request->input('dataI');
        $dataF = $request->input('dataF');
        $limite = $request->input('limite'); 

        $query = TKxExtratoModel::whereDate('DataDesembolso', '>=', $dataI)
                                    ->whereDate('DataDesembolso', '<=', $dataF)
                                    ->select(
                                        'UtCodigo',
                                        'DataDesembolso as CiFecha',
                                        'Lnr',
                                        'Cliente',
                                        'TXAProcePercentaValor',
                                        'ValorIVATaxaProcessamento',
                                        'TXAImprePercentaValor',
                                        'ValorIVATaxaImprevisto',
                                        'TXAProcePercentaValorAnte',
                                        'ValorIVATaxaProcessamentoAnte',
                                        'BaseOperacao',
                                        DB::raw('IFNULL(Bilhete, 999999999) as Bilhete'),
                                    );

        if ($limite !== null) {
            $query->limit((int) $limite);
        }

        $desembolsos = $query->get();

        return response()->json($desembolsos);
    }
    
    public function getComprovativos(Request $request)
    {
        $dataI = $request->input('dataI');
        $dataF = $request->input('dataF');
        $limite = $request->input('limite'); 

        $query = DB::table('comprovativos as c')
                    ->leftjoin('tkxextrato as ext', 'ext.Lnr','=','c.BuDadoOrigem')
                    ->whereDate('c.CiFecha', '>=', $dataI)
                    ->whereDate('c.CiFecha', '<=', $dataF)
                    ->where('c.idestado', 8)
                    ->whereNotIn('c.PoCodigo', ['S00','S02','S03','S06','S08'])
                    ->select(
                        'c.UtCodigo',
                        'c.BuDadoOrigem',
                        'c.PoCodigo',
                        'c.BuMontante',
                        DB::raw('IFNULL(c.Capital, 0) as CAPI'),
                        DB::raw('IFNULL(c.Juros, 0) as JURO'),
                        'c.CiFecha',
                        'c.infoadicional',
                        DB::raw('IFNULL(ext.Bilhete, 999999999) as Bilhete'),
                    );

        if ($limite !== null) {
            $query->limit((int) $limite);
        }

        $comprovativos = $query->get();

        return response()->json($comprovativos);
    }

    /*** Secção de recepção de dados para RD */
    public function carregartblRDprintCarteiraWriteOff(Request $request){
  
        $records = $request->input('records',[]); 

        $data = collect($records)->map(function ($record) {
            return [
                'Id' => $record['Id'],
                'DataReferencia' => $record['DataReferencia'],
                'TipoAgrupamento' => $record['TipoAgrupamento'],
                'Orden' => $record['Orden'],
                'EtiquetaPeriodoWO' => $record['EtiquetaPeriodoWO'],
                'BalancoInicial' => $record['BalancoInicial'],
                'BalancoFinal' => $record['BalancoFinal'],
                'RecuperadoAcumuladoAnterior' =>$record['RecuperadoAcumuladoAnterior'],
                'RecuperadoMes1' => $record['RecuperadoMes1'],
                'RecuperadoMes2' => $record['RecuperadoMes2'],
                'RecuperadoMes3' => $record['RecuperadoMes3'],
                'RecuperadoMes4' => $record['RecuperadoMes4'],
                'RecuperadoMes5' => $record['RecuperadoMes5'],
                'TotalRecuperado' => $record['TotalRecuperado'],
                'PctRecuperadoBalancoInicial' => $record['PctRecuperadoBalancoInicial'],
                'PctKR' => $record['PctKR'],
                'DataCriacao' => $record['DataCriacao'],
                'Activo' => $record['Activo']
            ];
        })->toArray(); 

        DB::transaction(function () use ($data) {

            // Eliminar os dados existentes
            TblRDPrintCarteiraWriteOffModel::query()->delete();

            // Inserir os novos
            if (!empty($data)) {
                TblRDPrintCarteiraWriteOffModel::insert($data);
            }
        });

        return response()->json([
            'success' => true,
            'processed' => count($data),
        ]);
    }

    public function carregartblRDprintDadosEstatisticos(Request $request){
        $records = $request->input('records');

        $data = collect($records)->map(function ($record) {
            return [
                'Id' => $record['Id'],
                'DataReferencia' => $record['DataReferencia'],
                'TipoAgrupamento' => $record['TipoAgrupamento'],
                'NivelEscopo' => $record['NivelEscopo'],
                'Ordem' => $record['Ordem'],
                'Homens_Qtd' => $record['Homens_Qtd'],
                'Mulheres_Qtd' => $record['Mulheres_Qtd'],
                'TempoSolicitacaoAprovacaoDias' => $record['TempoSolicitacaoAprovacaoDias'],
                'TempoAprovacaoDesembolsoDias' => $record['TempoAprovacaoDesembolsoDias'],
                'TempoAtendimentoTotalDias' => $record['TempoAtendimentoTotalDias'],
                'MediaDesembolsoOficial' => $record['MediaDesembolsoOficial'],
                'MediaCreditosOficial' => $record['MediaCreditosOficial'],
                'MediaDesembolsoCredito' => $record['MediaDesembolsoCredito'],
                'MediaPrazoMeses' => $record['MediaPrazoMeses'],
                'MediaPrestacao' => $record['MediaPrestacao'],
                'MediaReembolso' => $record['MediaReembolso'],
                'MediaDivida' => $record['MediaDivida'],
                'TaxaReembolsoPct' => $record['TaxaReembolsoPct'],
                'TaxaCumprimentoPct' => $record['TaxaCumprimentoPct'],
                'TaxaRecuperacaoPct' => $record['TaxaRecuperacaoPct'],
                'DataCriacao' => $record['DataCriacao'],
                'Activo' => $record['Activo']
            ];
        })->toArray();

        DB::transaction(function () use ($data) {

            // Eliminar os dados existentes
            TblRDprintDadosEstatisticosModel::query()->delete();

            // Inserir os novos
            if (!empty($data)) {
                TblRDprintDadosEstatisticosModel::insert($data);
            }
        });

        return response()->json([
            'success' => true,
            'processed' => count($data),
        ]);
    }

    public function carregartblRDprintDesembolsosReembolsos12meses(Request $request){
        $records = $request->input('records');

        $data = collect($records)->map(function ($record) {
            return [
                'Id' => $record['Id'],
                'DataReferencia' => $record['DataReferencia'],
                'NomeDoMes' => $record['NomeDoMes'],
                'DesembolsoValor' => $record['DesembolsoValor'],
                'ReembolsoValor' => $record['ReembolsoValor'],
                'DataCriacao' => $record['DataCriacao'],
                'Activo' => $record['Activo']
            ];
        })->toArray();

        DB::transaction(function () use ($data) {

            // Eliminar os dados existentes
            TblRDPrintDesembolsosReembolsos12MesesModel::query()->delete();

            // Inserir os novos
            if (!empty($data)) {
                TblRDPrintDesembolsosReembolsos12MesesModel::insert($data);
            }
        });

        return response()->json([
            'success' => true,
            'processed' => count($data),
        ]);
    }
    
    public function carregartblRDprintKixiCredito(Request $request){
        $records = $request->input('records');

        $data = collect($records)->map(function ($record) {
            return [
                'Id' => $record['Id'],
                'DataReferencia' => $record['DataReferencia'],	
                'BalancoValor' => $record['BalancoValor'],	
                'CreditosQuantidade' => $record['CreditosQuantidade'],	
                'CreditosNovos' => $record['CreditosNovos'],	
                'TaxaJuros1' => $record['TaxaJuros1'],	
                'TaxaJuros2' => $record['TaxaJuros2'],	
                'CreditosAteKz2000_Qtd' => $record['CreditosAteKz2000_Qtd'],	
                'CreditosAteKz2000_Valor' => $record['CreditosAteKz2000_Valor'],	
                'ClientesQuantidade' => $record['ClientesQuantidade'],	
                'ClientesNovos' => $record['ClientesNovos'],	
                'ClientesAteKz2000' => $record['ClientesAteKz2000'],	
                'NPLPercentual' => $record['NPLPercentual'],	
                'NPLValor' => $record['NPLValor'],	
                'PAR1Percentual' => $record['PAR1Percentual'],	
                'PAR1Valor' => $record['PAR1Valor'],	
                'PAR30Percentual' => $record['PAR30Percentual'],	
                'PAR30Valor' => $record['PAR30Valor'],	
                'ProvisaoValor' => $record['ProvisaoValor'],	
                'ProvisaoCoberturaPerc' => $record['ProvisaoCoberturaPerc'],	
                'ProvisaoDiasAtraso' => $record['ProvisaoDiasAtraso'],	
                'TaxaReembolsoPercentual' => $record['TaxaReembolsoPercentual'],	
                'TaxaCumprimentoPercentual' => $record['TaxaCumprimentoPercentual'],	
                'TaxaRecuperacaoPercentual' => $record['TaxaRecuperacaoPercentual'],	
                'Desembolsos' => $record['Desembolsos'],	
                'Reembolso' => $record['Reembolso'],	
                'CreditosNovos_Mais' => $record['CreditosNovos_Mais'],	
                'CreditosNovos_Menos' => $record['CreditosNovos_Menos'],	
                'ClientesNovos_Mais' => $record['ClientesNovos_Mais'],	
                'ClientesNovos_Menos' => $record['ClientesNovos_Menos'],	
                'NPL_Mais' => $record['NPL_Mais'],	
                'NPL_Menos' => $record['NPL_Menos'],	
                'PAR1_Mais' => $record['PAR1_Mais'],	
                'PAR1_Menos' => $record['PAR1_Menos'],	
                'PAR30_Mais' => $record['PAR30_Mais'],	
                'PAR30_Menos' => $record['PAR30_Menos'],	
                'Provisao_Mais' => $record['Provisao_Mais'],	
                'Provisao_Menos' => $record['Provisao_Menos'],	
                'TaxaReembolso_Mais' => $record['TaxaReembolso_Mais'],	
                'TaxaReembolso_Menos' => $record['TaxaReembolso_Menos'],	
                'TaxaCumprimento_Mais' => $record['TaxaCumprimento_Mais'],	
                'TaxaCumprimento_Menos' => $record['TaxaCumprimento_Menos'],	
                'TaxaRecuperacao_Mais' => $record['TaxaRecuperacao_Mais'],	
                'TaxaRecuperacao_Menos' => $record['TaxaRecuperacao_Menos'],	
                'VariacaoPercentual' => $record['VariacaoPercentual'],	
                'VariacaoCreditosNovos' => $record['VariacaoCreditosNovos'],	
                'VariacaoClientesNovos' => $record['VariacaoClientesNovos'],	
                'VariacaoNPL' => $record['VariacaoNPL'],	
                'VariacaoPAR1' => $record['VariacaoPAR1'],	
                'VariacaoPAR30' => $record['VariacaoPAR30'],	
                'VariacaoProvisao' => $record['VariacaoProvisao'],	
                'VariacaoTaxaReembolso' => $record['VariacaoTaxaReembolso'],	
                'VariacaoTaxaCumprimento' => $record['VariacaoTaxaCumprimento'],	
                'VariacaoTaxaRecuperacao' => $record['VariacaoTaxaRecuperacao'],	
                'DataCriacao' => $record['DataCriacao'],	
                'Activo' => $record['Activo']
            ];
        })->toArray();

        DB::transaction(function () use ($data) {

            // Eliminar os dados existentes
            TblRDprintKixiCreditoModel::query()->delete();

            // Inserir os novos
            if (!empty($data)) {
                TblRDprintKixiCreditoModel::insert($data);
            }
        });

        return response()->json([
            'success' => true,
            'processed' => count($data),
        ]);
    }
    
    public function carregartblRDprintMovimentoPrestacao(Request $request){
        $records = $request->input('records');

        $data = collect($records)->map(function ($record) {
            return [
                'Id' => $record['Id'],
                'DataReferencia' => $record['DataReferencia'],	
                'TipoAgrupamento' => $record['TipoAgrupamento'],	
                'Orden' => $record['Orden'],	
                'PeriodoPrestacao' => $record['PeriodoPrestacao'],	
                'CapIniValor' => $record['CapIniValor'],	
                'CapDesValor' => $record['CapDesValor'],	
                'CapReeValor' => $record['CapReeValor'],	
                'CapFimValor' => $record['CapFimValor'],	
                'JurIniValor' => $record['JurIniValor'],	
                'JurDesValor' => $record['JurDesValor'],	
                'JurReeValor' => $record['JurReeValor'],	
                'JurFimValor' => $record['JurFimValor'],	
                'TotalRecuperado' => $record['TotalRecuperado'],	
                'PctRecuperado' => $record['PctRecuperado'],	
                'TxRecuperacao' => $record['TxRecuperacao'],	
                'DataCriacao' => $record['DataCriacao'],	
                'Activo' => $record['Activo']
            ];
        })->toArray();

        DB::transaction(function () use ($data) {

            // Eliminar os dados existentes
            TblRDPrintMovimentoPrestacaoModel::query()->delete();

            // Inserir os novos
            if (!empty($data)) {
                TblRDPrintMovimentoPrestacaoModel::insert($data);
            }
        });

        return response()->json([
            'success' => true,
            'processed' => count($data),
        ]);
    }

    public function carregartblRDprintProduto(Request $request){
        $records = $request->input('records');

        $data = collect($records)->map(function ($record) {
            return [
                'Id' => $record['Id'],
                'DataReferencia' => $record['DataReferencia'],	
                'TipoAgrupamento' => $record['TipoAgrupamento'],	
                'NomeProduto' => $record['NomeProduto'],	
                'TipoDireccao' => $record['TipoDireccao'],	
                'DirectorNome' => $record['DirectorNome'],	
                'OA_Qtd' => $record['OA_Qtd'],	
                'PA_Qtd' => $record['PA_Qtd'],	
                'BalancoValor' => $record['BalancoValor'],	
                'CreditosTotal_Qtd' => $record['CreditosTotal_Qtd'],	
                'CreditosNovos_Qtd' => $record['CreditosNovos_Qtd'],	
                'ClientesTotal_Qtd' => $record['ClientesTotal_Qtd'],	
                'ClientesNovos_Qtd' => $record['ClientesNovos_Qtd'],	
                'PAR1_Valor' => $record['PAR1_Valor'],	
                'PAR1_Percentual' => $record['PAR1_Percentual'],	
                'PAR30_Valor' => $record['PAR30_Valor'],	
                'PAR30_Percentual' => $record['PAR30_Percentual'],	
                'DesembolsoValor' => $record['DesembolsoValor'],	
                'TempoAtendimentodias' => $record['TempoAtendimentodias'],	
                'ReembolsoValor' => $record['ReembolsoValor'],	
                'ProvisaoValor' => $record['ProvisaoValor'],	
                'ProvisaoClasseRisco' => $record['ProvisaoClasseRisco'],	
                'GestaoValor' => $record['GestaoValor'],	
                'GestaoSufixo' => $record['GestaoSufixo'],	
                'DataCriacao' => $record['DataCriacao'],	
                'Activo' => $record['Activo']
            ];
        })->toArray();

        DB::transaction(function () use ($data) {

            // Eliminar os dados existentes
            TblRDPrintProdutoModel::query()->delete();

            // Inserir os novos
            if (!empty($data)) {
                TblRDPrintProdutoModel::insert($data);
            }
        });

        return response()->json([
            'success' => true,
            'processed' => count($data),
        ]);
    }


}
