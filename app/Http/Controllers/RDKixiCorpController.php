<?php

namespace App\Http\Controllers;

use App\Models\TblRDprintKixiCreditoModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class RDKixiCorpController extends Controller
{
    /**
     * Relatório Global (KIX - Instituição 100%)
     */
    public function viewReportRD(Request $request)
    {
        return $this->renderReportPorDireccao('KIX');
    }

    /**
     * Direção: Particulares & Parcerias (DPP)
     */
    public function viewReportRDDPP(Request $request)
    {
        return $this->renderReportPorDireccao('DPP');
    }

    /**
     * Direção: Operações (DOP)
     */
    public function viewReportRDDOP(Request $request)
    {
        return $this->renderReportPorDireccao('DOP');
    }

    /**
     * Direção: Outros (OUT)
     */
    public function viewReportRDOUT(Request $request)
    {
        return $this->renderReportPorDireccao('OUT');
    }

    /**
     * Método centralizado que filtra a tabela principal por TipoDireccao ('KIX', 'DPP', 'DOP', 'OUT')
     * e carrega os detalhes das tabelas correspondentes.
     */
    private function renderReportPorDireccao(string $tipoDireccao)
    {
        $authenticatedUser = Auth::user();
        $tipoDireccao = strtoupper(trim($tipoDireccao)); // 'KIX', 'DPP', 'DOP', 'OUT'
        $sufixo = ($tipoDireccao === 'KIX') ? '' : $tipoDireccao; // '', 'DPP', 'DOP', 'OUT'

        // 1. Histórico consolidado filtrado por TipoDireccao na tabela principal
        $historico = TblRDprintKixiCreditoModel::getHistoricoPorDireccao($tipoDireccao);

        $ultimoRegisto = $historico->last();
        $ultimaDataRef = $ultimoRegisto ? $ultimoRegisto->DataReferencia : null;
        $directorNome = $ultimoRegisto->DirectorNome ?? null;

        // 2. Regiões / Agências
        $agencias = $ultimaDataRef
            ? $this->consultarTabelaSecundaria('tblRDprintRegional', $sufixo, $tipoDireccao, $ultimaDataRef)
            : [];

        // 3. Produtos
        $produtos = $ultimaDataRef
            ? $this->consultarTabelaSecundaria('tblRDprintProduto', $sufixo, $tipoDireccao, $ultimaDataRef)
            : [];

        // 4. Provisão detalhada de produtos
        $provisoesProdutos = $ultimaDataRef
            ? $this->consultarTabelaSecundaria('tblRDprintProdutoProvisao', $sufixo, $tipoDireccao, $ultimaDataRef)
            : [];

        // 5. Desembolsos e Reembolsos (12 meses)
        // 5. Desembolsos e Reembolsos (12 meses) — sem filtro de data: as 12 últimas linhas activas da direcção
        $desembolsosReembolsos12Meses = $this->consultarTabelaSecundaria(
            'tblRDprintDesembolsosReembolsos12meses',
            $sufixo,
            $tipoDireccao,
            null,
            48
        );
        /*  $desembolsosReembolsos12Meses = $ultimaDataRef
              ? $this->consultarTabelaSecundaria('tblRDprintDesembolsosReembolsos12meses', $sufixo, $tipoDireccao, $ultimaDataRef)
              : [];*/

        // 6. Dados Estatísticos
        $dadosEstatisticos = $ultimaDataRef
            ? $this->consultarTabelaSecundaria('tblRDprintDadosEstatisticos', $sufixo, $tipoDireccao, $ultimaDataRef)
            : [];

        // 7. Carteira Write-Off
        $carteiraWriteOff = $ultimaDataRef
            ? $this->consultarTabelaSecundaria('tblRDprintCarteiraWriteOff', $sufixo, $tipoDireccao, $ultimaDataRef)
            : [];

        // 8. Movimento de Prestação
        $movimentoPrestacao = $ultimaDataRef
            ? $this->consultarTabelaSecundaria('tblRDprintMovimentoPrestacao', $sufixo, $tipoDireccao, $ultimaDataRef)
            : [];

        // 9. Exposição de Risco por Centros de Custo
        $riscoExposicaoCentrosCusto = $ultimaDataRef
            ? $this->consultarTabelaSecundaria('tblRDRiscoExposicaoCentrosCusto', $sufixo, $tipoDireccao, $ultimaDataRef)
            : [];

        // 10. Risco Top Clientes (Apenas TOP15_GERAL na Página Inicial 'KIX'; restante por direção)
        $riscoTopClientes = $ultimaDataRef
            ? $this->consultarTopClientesPorDireccao($tipoDireccao, $ultimaDataRef)
            : [];

        // Usa sempre o mesmo componente moderno ReportRD (evita duplicar 4 ficheiros Vue)
        return Inertia::render('ReportRD', [
            'direccaoActiva' => ($tipoDireccao === 'KIX') ? 'GLOBAL' : $tipoDireccao,
            'directorNome' => $directorNome,
            'historicoRD' => $historico,
            'agenciasRD' => $agencias,
            'produtosRD' => $produtos,
            'provisoesProdutosRD' => $provisoesProdutos,
            'desembolsosReembolsos12MesesRD' => $desembolsosReembolsos12Meses,
            'dadosEstatisticosRD' => $dadosEstatisticos,
            'carteiraWriteOffRD' => $carteiraWriteOff,
            'movimentoPrestacaoRD' => $movimentoPrestacao,
            'riscoExposicaoCentrosCustoRD' => $riscoExposicaoCentrosCusto,
            'riscoTopClientesRD' => $riscoTopClientes,
            'user' => $authenticatedUser,
        ]);
    }

    /**
     * Filtra as tabelas auxiliares respeitando a coluna onde o agrupamento/direção está gravado:
     * - Na Página Inicial ('KIX'): filtra estritamente pela coluna que contém 'KIX' (TipoAgrupamento ou TipoDireccao).
     * - Nas Direções ('DPP', 'DOP', 'OUT'): lê tabela com sufixo se existir, ou filtra pela coluna da direção.
     */
    private function consultarTabelaSecundaria(string $tabelaBase, string $sufixo, string $tipoDireccao, $dataReferencia = null, ?int $ultimasLinhas = null)
    {
        $tabelaComSufixo = "{$tabelaBase}{$sufixo}";

        // Aplica a data (se existir) e, opcionalmente, devolve só as N últimas linhas por ordem cronológica
        $finalizar = function ($query) use ($dataReferencia, $ultimasLinhas) {
            $query->where('Activo', 1);

            if ($dataReferencia !== null) {
                $query->where('DataReferencia', $dataReferencia);
            }

            if ($ultimasLinhas !== null) {
                return $query->orderByDesc('Id')->limit($ultimasLinhas)->get()->reverse()->values();
            }

            return $query->get();
        };

        if ($sufixo !== '' && Schema::hasTable($tabelaComSufixo)) {
            return $finalizar(DB::table($tabelaComSufixo));
        }

        if (!Schema::hasTable($tabelaBase)) {
            return [];
        }

        $query = DB::table($tabelaBase);

        $temTipoAgrupamento = Schema::hasColumn($tabelaBase, 'TipoAgrupamento');
        $temTipoDireccao = Schema::hasColumn($tabelaBase, 'TipoDireccao');
        $temCcDireccao = Schema::hasColumn($tabelaBase, 'ccDireccao');

        if ($tipoDireccao === 'KIX') {
            if ($temTipoAgrupamento && $temTipoDireccao) {
                $query->where(function ($q) {
                    $q->where('TipoAgrupamento', 'KIX')
                        ->orWhere('TipoDireccao', 'KIX');
                });
            } elseif ($temTipoAgrupamento) {
                $query->where('TipoAgrupamento', 'KIX');
            } elseif ($temTipoDireccao) {
                $query->where('TipoDireccao', 'KIX');
            }
        } else {
            if ($temTipoDireccao) {
                $query->where('TipoDireccao', $tipoDireccao);
            } elseif ($temTipoAgrupamento) {
                $query->where('TipoAgrupamento', $tipoDireccao);
            } elseif ($temCcDireccao) {
                $query->where('ccDireccao', $tipoDireccao);
            }
        }

        return $finalizar($query);
    }
    /*private function consultarTabelaSecundaria(string $tabelaBase, string $sufixo, string $tipoDireccao, $dataReferencia)
    {
        $tabelaComSufixo = "{$tabelaBase}{$sufixo}";

        if ($sufixo !== '' && Schema::hasTable($tabelaComSufixo)) {
            return DB::table($tabelaComSufixo)
                ->where('DataReferencia', $dataReferencia)
                ->where('Activo', 1)
                ->get();
        }

        if (!Schema::hasTable($tabelaBase)) {
            return [];
        }

        $query = DB::table($tabelaBase)
            ->where('DataReferencia', $dataReferencia)
            ->where('Activo', 1);

        $temTipoAgrupamento = Schema::hasColumn($tabelaBase, 'TipoAgrupamento');
        $temTipoDireccao    = Schema::hasColumn($tabelaBase, 'TipoDireccao');
        $temCcDireccao      = Schema::hasColumn($tabelaBase, 'ccDireccao');

        if ($tipoDireccao === 'KIX') {
            // Na Página Inicial (KIX), filtra estritamente pela coluna que guarda 'KIX'
            if ($temTipoAgrupamento && $temTipoDireccao) {
                $query->where(function ($q) {
                    $q->where('TipoAgrupamento', 'KIX')
                      ->orWhere('TipoDireccao', 'KIX');
                });
            } elseif ($temTipoAgrupamento) {
                $query->where('TipoAgrupamento', 'KIX');
            } elseif ($temTipoDireccao) {
                $query->where('TipoDireccao', 'KIX');
            }
        } else {
            // Nas direções específicas (DPP, DOP, OUT)
            if ($temTipoDireccao) {
                $query->where('TipoDireccao', $tipoDireccao);
            } elseif ($temTipoAgrupamento) {
                $query->where('TipoAgrupamento', $tipoDireccao);
            } elseif ($temCcDireccao) {
                $query->where('ccDireccao', $tipoDireccao);
            }
        }

        return $query->get();
    }*/

    /**
     * Filtra a secção Top 15 Clientes:
     * - Na Página Inicial ('KIX'): retorna APENAS 'TOP15_GERAL'.
     * - Nas Direções ('DOP', 'DPP', 'OUT'): retorna APENAS o ranking daquela direção ('TOP15_DOP', 'TOP15_DPP', etc.).
     */
    private function consultarTopClientesPorDireccao(string $tipoDireccao, $dataReferencia)
    {
        if (!Schema::hasTable('tblRDRiscoTopClientes')) {
            return [];
        }

        $query = DB::table('tblRDRiscoTopClientes')
            ->where('DataReferencia', $dataReferencia)
            ->where('Activo', 1);

        if ($tipoDireccao === 'KIX') {
            $query->where('TipoRanking', 'TOP15_GERAL');
        } else {
            $rankingAlvo = "TOP15_{$tipoDireccao}";
            $query->where(function ($q) use ($rankingAlvo, $tipoDireccao) {
                $q->where('TipoRanking', $rankingAlvo)
                    ->orWhere(function ($sub) use ($tipoDireccao) {
                        $sub->whereNull('TipoRanking')
                            ->where('ccDireccao', $tipoDireccao);
                    });
            });
        }

        return $query->orderBy('Posicao', 'asc')->get();
    }
}
