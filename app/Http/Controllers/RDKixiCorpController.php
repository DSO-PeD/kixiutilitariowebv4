<?php

namespace App\Http\Controllers;

use App\Models\TblRDprintRiscoExposicaoCentrosCustoModel;
use App\Models\TblRDRiscoTopClientesModel;
use Illuminate\Http\Request;
use App\Models\TblRDprintKixiCreditoModel;
use App\Models\TblRDprintRegionalModel;
use App\Models\TblRDPrintProdutoModel;
use App\Models\TblRDPrintProdutoProvisaoModel;
use App\Models\TblRDPrintDesembolsosReembolsos12MesesModel;
use App\Models\TblRDprintDadosEstatisticosModel;
use App\Models\TblRDPrintCarteiraWriteOffModel;
use App\Models\TblRDPrintMovimentoPrestacaoModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RDKixiCorpController extends Controller
{
    /**
     * Relatório Global (Instituição 100%)
     */
    public function viewReportRD(Request $request)
    {
        return $this->renderReportPorDireccao('', 'ReportRD');
    }

    /**
     * Direção: Particulares & Parcerias (DPP)
     */
    public function viewReportRDDPP(Request $request)
    {
        return $this->renderReportPorDireccao('DPP', 'ReportRDDPP');
    }

    /**
     * Direção: Operações (DOP)
     */
    public function viewReportRDDOP(Request $request)
    {
        return $this->renderReportPorDireccao('DOP', 'ReportRDDOP');
    }

    /**
     * Direção: Outros (OUT)
     */
    public function viewReportRDOUT(Request $request)
    {
        return $this->renderReportPorDireccao('OUT', 'ReportRDOUT');
    }

    /**
     * Método centralizado que lê as tabelas correspondentes ao sufixo ('', 'DPP', 'DOP', 'OUT')
     */
    private function renderReportPorDireccao(string $sufixo, string $componenteVue)
    {
        $authenticatedUser = Auth::user();
        $sufixo = strtoupper(trim($sufixo)); // '', 'DPP', 'DOP', 'OUT'

        // 1. Histórico global consolidado da direção (ex: tblRDprintKixiCreditoDPP)
        $historico = DB::table("tblRDprintKixiCredito{$sufixo}")
            ->where('Activo', 1)
            ->orderBy('DataReferencia', 'asc')
            ->get();

        $ultimaDataRef = $historico->last() ? $historico->last()->DataReferencia : null;

        // 2. Regiões / Agências
        $agencias = $ultimaDataRef
            ? DB::table("tblRDprintRegional{$sufixo}")
                ->where('DataReferencia', $ultimaDataRef)
                ->where('Activo', 1)
            //    ->orderBy('Orden', 'asc')
                ->get()
            : [];

        // 3. Produtos
        $produtos = $ultimaDataRef
            ? DB::table("tblRDprintProduto{$sufixo}")
                ->where('DataReferencia', $ultimaDataRef)
                ->where('Activo', 1)
            //    ->orderBy('Orden', 'asc')
                ->get()
            : [];

        // 4. Provisão detalhada de produtos
        $provisoesProdutos = $ultimaDataRef
            ? DB::table("tblRDprintProdutoProvisao{$sufixo}")
                ->where('DataReferencia', $ultimaDataRef)
                ->where('Activo', 1)
            //    ->orderBy('Orden', 'asc')
                ->get()
            : [];

        // 5. Desembolsos e Reembolsos (12 meses)
        $desembolsosReembolsos12Meses = $ultimaDataRef
            ? DB::table("tblRDprintDesembolsosReembolsos12meses{$sufixo}")
                ->where('DataReferencia', $ultimaDataRef)
                ->where('Activo', 1)
            //    ->orderBy('Id', 'asc')
                ->get()
            : [];

        // 6. Dados Estatísticos
        $dadosEstatisticos = $ultimaDataRef
            ? DB::table("tblRDprintDadosEstatisticos{$sufixo}")
                ->where('DataReferencia', $ultimaDataRef)
                ->where('Activo', 1)
            //    ->orderBy('Ordem', 'asc')
                ->get()
            : [];

        // 7. Carteira Write-Off
        $carteiraWriteOff = $ultimaDataRef
            ? DB::table("tblRDprintCarteiraWriteOff{$sufixo}")
                ->where('DataReferencia', $ultimaDataRef)
                ->where('Activo', 1)
            //    ->orderBy('Orden', 'asc')
                ->get()
            : [];

        // 8. Movimento de Prestação
        $movimentoPrestacao = $ultimaDataRef
            ? DB::table("tblRDprintMovimentoPrestacao{$sufixo}")
                ->where('DataReferencia', $ultimaDataRef)
                ->where('Activo', 1)
            //    ->orderBy('Orden', 'asc')
                ->get()
            : [];

        // 9. Exposição de Risco por Centros de Custo
        $riscoExposicaoCentrosCusto = $ultimaDataRef
            ? DB::table("tblrdriscoexposicaocentroscusto{$sufixo}")
                ->where('DataReferencia', $ultimaDataRef)
                ->where('Activo', 1)
            //    ->orderBy('PosicaoRisco', 'asc')
                ->get()
            : [];

        // 10. Risco Top Clientes
        $riscoTopClientes = $ultimaDataRef
            ? DB::table("tblRDRiscoTopClientes{$sufixo}")
                ->where('DataReferencia', $ultimaDataRef)
                ->where('Activo', 1)
            //    ->orderBy('Posicao', 'asc')
                ->get()
            : [];

        return Inertia::render($componenteVue, [
            'direccaoActiva'                 => $sufixo ?: 'GLOBAL',
            'historicoRD'                    => $historico,
            'agenciasRD'                     => $agencias,
            'produtosRD'                     => $produtos,
            'provisoesProdutosRD'            => $provisoesProdutos,
            'desembolsosReembolsos12MesesRD' => $desembolsosReembolsos12Meses,
            'dadosEstatisticosRD'           => $dadosEstatisticos,
            'carteiraWriteOffRD'             => $carteiraWriteOff,
            'movimentoPrestacaoRD'           => $movimentoPrestacao,
            'riscoExposicaoCentrosCustoRD'   => $riscoExposicaoCentrosCusto,
            'riscoTopClientesRD'             => $riscoTopClientes,
            'user'                           => $authenticatedUser,
        ]);
    }
    /*
    public function viewReportRD(Request $request)
    {
        $authenticatedUser = Auth::user();

        // 1. Buscar histórico global consolidado
        $historico = TblRDprintKixiCreditoModel::orderBy('DataReferencia', 'asc')
            ->where('Activo', 1)
            ->get();

        $ultimaDataRef = $historico->last() ? $historico->last()->DataReferencia : null;

        // 2. Buscar regiões / agências
        $agencias = $ultimaDataRef
            ? TblRDprintRegionalModel::getPorDataReferencia($ultimaDataRef)
            : [];

        // 3. Buscar produtos
        $produtos = $ultimaDataRef
            ? TblRDPrintProdutoModel::getPorDataReferencia($ultimaDataRef)
            : [];

        // 4. Buscar provisão detalhada de produtos
        $provisoesProdutos = $ultimaDataRef
            ? TblRDPrintProdutoProvisaoModel::getPorDataReferencia($ultimaDataRef)
            : [];

        // 5. Buscar histórico de desembolsos e reembolsos dos últimos 12 meses
        $desembolsosReembolsos12Meses = $ultimaDataRef
            ? TblRDPrintDesembolsosReembolsos12MesesModel::getPorDataReferencia($ultimaDataRef)
            : [];

        // 6. Buscar dados estatísticos
        $dadosEstatisticos = $ultimaDataRef
            ? TblRDprintDadosEstatisticosModel::getPorDataReferencia($ultimaDataRef)
            : [];

        // 7. Buscar carteira Write-Off
        $carteiraWriteOff = $ultimaDataRef
            ? TblRDPrintCarteiraWriteOffModel::getPorDataReferencia($ultimaDataRef)
            : [];

        // 8. Buscar movimento de prestação
        $movimentoPrestacao = $ultimaDataRef
            ? TblRDPrintMovimentoPrestacaoModel::getPorDataReferencia($ultimaDataRef)
            : [];

        // 9. Buscar Exposição de Risco por Centros de Custo (ordenado por PosicaoRisco)
        $riscoExposicaoCentrosCusto = $ultimaDataRef
            ? TblRDprintRiscoExposicaoCentrosCustoModel::where('DataReferencia', $ultimaDataRef)
                ->where('Activo', 1)
                ->orderBy('PosicaoRisco', 'asc')
                ->get()
            : [];

        // 10. Buscar Risco Top Clientes (DOP, DPP e Geral)
        $riscoTopClientes = $ultimaDataRef
            ? TblRDRiscoTopClientesModel::where('DataReferencia', $ultimaDataRef)
                ->where('Activo', 1)
                ->orderBy('Posicao', 'asc')
                ->get()
            : [];

        return Inertia::render('ReportRD', [
            'historicoRD'                    => $historico,
            'agenciasRD'                     => $agencias,
            'produtosRD'                     => $produtos,
            'provisoesProdutosRD'            => $provisoesProdutos,
            'desembolsosReembolsos12MesesRD' => $desembolsosReembolsos12Meses,
            'dadosEstatisticosRD'           => $dadosEstatisticos,
            'carteiraWriteOffRD'             => $carteiraWriteOff,
            'movimentoPrestacaoRD'           => $movimentoPrestacao,
            'riscoExposicaoCentrosCustoRD'   => $riscoExposicaoCentrosCusto,
            'riscoTopClientesRD'             => $riscoTopClientes,
            'user'                           => $authenticatedUser
        ]);
    }
*/


}
