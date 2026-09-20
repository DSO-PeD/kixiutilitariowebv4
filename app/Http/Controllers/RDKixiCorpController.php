<?php

namespace App\Http\Controllers;

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
use Inertia\Inertia;

class RDKixiCorpController extends Controller
{
    public function viewReportRD(Request $request)
    {
        $authenticatedUser = Auth::user();

        // 1. Buscar histórico global
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

        return Inertia::render('ReportRD', [
            'historicoRD'                    => $historico,
            'agenciasRD'                     => $agencias,
            'produtosRD'                     => $produtos,
            'provisoesProdutosRD'            => $provisoesProdutos,
            'desembolsosReembolsos12MesesRD' => $desembolsosReembolsos12Meses,
            'dadosEstatisticosRD'           => $dadosEstatisticos,
            'carteiraWriteOffRD'             => $carteiraWriteOff,
            'movimentoPrestacaoRD'           => $movimentoPrestacao,
            'user'                           => $authenticatedUser
        ]);
    }
}
