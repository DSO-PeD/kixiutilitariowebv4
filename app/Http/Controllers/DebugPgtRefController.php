<?php

namespace App\Http\Controllers;

use App\Models\ComprovativoModel;
use App\Models\CpvtReconciliacaoModel;
use App\Models\PgtRefNotificacaoModel;
use App\Models\ReferenciaPGTModel;
use App\Models\TKxClProdutoModel;
use App\Models\TKxExtratoModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DebugPgtRefController extends Controller
{
    public function carregarPagamentoPorReferencia($periodoInicio,$periodoFim)
    {
        $cont = 0;

        $refs = DB::table('pgtrefnotificacao as p')
                ->whereBetween('p.idLogSistema', [$periodoInicio, $periodoFim])
                ->whereNotIn('p.refPagamento', function ($query) {
                    $query->select('c.refPagamento')
                        ->from('comprovativos as c')
                        ->where('c.UtCodigo', 'Izipay')
                        ->whereColumn('c.periodo_trans_pgr', 'p.idLogSistema');
                })
                ->get();

 
        foreach($refs as $ref){
            try{               

                $referenciaKIXI = $ref->refPagamento;
                $dataFormatadaREF = Carbon::parse($ref->dataTransaccaoCliente)->format('dmY');

                // Buscar referência
                $ExisteReferencia = $this->buscarReferencia($referenciaKIXI);

                // Se não encontrar referencia, pula para outro passo
                if (empty($ExisteReferencia) || !$ExisteReferencia['encontrada']) {
                    Log::warning('Referência não encontrada', [
                        'refPagamento' => $referenciaKIXI
                    ]);
                    continue;
                }

                // Processar pagamento
                $this->processarPagamento($ref, $ExisteReferencia, $referenciaKIXI, $dataFormatadaREF);
                ++$cont;

            } catch (\Throwable $e){
                // Log error but DO NOT stop the loop
                Log::error('Erro ao processar referência', [
                    'refPagamento' => $ref->refPagamento,
                    'error' => $e->getMessage(),
                ]);

                continue;
            }
        }
        
        return $cont;
    }

    // Métodos auxiliar: #1
    private function buscarReferencia(string $referenciaKIXI): array
    {
        // Buscar na tabela principal primeiro
        $referencia = TKxExtratoModel::where('referenciapagamento', $referenciaKIXI)->first();

        if ($referencia) {
            $produto = TKxClProdutoModel::where('PoAgrupado', $referencia->Produto)
                ->where('Estado', 1)
                ->first();

            return [
                'encontrada' => true,
                'dados' => $referencia,
                'lnr' => $referencia->Lnr,
                'metodologia' => $produto->Metodologia ?? 'DJA',
                'telefone' => $produto->Telefone ?? 'Desconhecido',
                'tipo' => 'extrato'
            ];
        }

        // Buscar na tabela de referências manuais
        $referenciaManual = ReferenciaPGTModel::where('referencia', $referenciaKIXI)->first();
        
        if ($referenciaManual) {
            $ref = [
                'encontrada' => true,
                'dados' => $referenciaManual,
                'lnr' => $referenciaManual->BuDadoOrigem,
                'metodologia' => $referenciaManual->PoCodigo ?? 'DJA',
                'telefone' => $produto->telefone ?? 'Desconhecido',
                'tipo' => 'manual'
            ];
            return $ref;
        }

        return ['encontrada' => false];
    }

    // Métodos auxiliar: #2
    private function atualizarReferenciaManual(int $idReferencia, float $montantePago): void
    {
        ReferenciaPGTModel::where('id', $idReferencia)->update([
            'montantepago' => $montantePago,
            'idestado' => 22,
            'updated_at' => now()
        ]);

        Log::info('Referência manual atualizada', [
            'id' => $idReferencia,
            'montantepago' => $montantePago,
            'idestado' => 22
        ]);
    }

    private function processarPagamento($item, array $dadosReferencia, string $referenciaKIXI, string $dataFormatadaREF): void
    {
        if ($item) {
            // Se for referência manual, atualizar os campos
            if ($dadosReferencia['tipo'] === 'manual') {
                // Calcular o montante total já pago para esta referência
                $valorPago = PgtRefNotificacaoModel::where('refPagamento', $referenciaKIXI)->sum('montantePago');
                
                $this->atualizarReferenciaManual($dadosReferencia['dados']->id, $valorPago);

            }

            $this->criarComprovativoEReconciliacao($item, $dadosReferencia, $dataFormatadaREF,$referenciaKIXI);
        }
    }

    private function criarComprovativoEReconciliacao($item, array $dadosReferencia, string $dataFormatadaREF, string $referenciaKIXI): void
    {
        $dataFormatadaBuData = Carbon::parse($item->dataTransaccaoCliente)->format('Y-m-d H:i:s');
        $codigo_voucher_dia = 'BMA' . $dataFormatadaREF;
        $codigo_voucher = 'PREF' . $dataFormatadaREF . '/' . $dadosReferencia['lnr'];
        
        // Criar comprovativo - ajuste para nomes de colunas diferentes entre tabelas
        $comprovativo = ComprovativoModel::create([
            'CiFecha' => $dataFormatadaBuData,
            'UtCodigo' => 'Izipay',
            'BaCodigo' => 3,
            'TtCodigo' => 'DJA',
            'FormaPago' => 8,
            'PoCodigo' => $dadosReferencia['metodologia'],
            'BuDadoOrigem' => $dadosReferencia['lnr'],
            'BuReferencia' => $codigo_voucher_dia,
            'BuReferenciaTransacao' => $codigo_voucher,
            'BuMontante' => $item->montantePago,
            'BuData' => $dataFormatadaBuData,
            'BuContaBancaria' => '2972939510001',
            'Eliminado' => 0,
            'idestado' => 8,
            'BaseOperacao' => $dadosReferencia['dados']->BaseOperacao,
            'infoadicional' => $dadosReferencia['dados']->Cliente ?? $dadosReferencia['dados']->nomecliente ?? 'Desconhecido',
            'filecomprovativo' => 'Sem extrato',
            'telefonecliente' => $dadosReferencia['telefone'],
            'periodo_trans_pgr' => $item->idLogSistema,
            'refPagamento' => $referenciaKIXI
        ]);

        if ($comprovativo) {
            $this->criarReconciliacao($comprovativo, $codigo_voucher_dia, $codigo_voucher,$dataFormatadaBuData);
        }
    }

    private function criarReconciliacao($comprovativo, string $codigo_voucher_dia, string $codigo_voucher,$data): void
    {
        // Inserção para reconciliação automática
        CpvtReconciliacaoModel::create([
            'datareconciliacao' => $data,
            'CodigoConta' => 79,
            'voucher' => $codigo_voucher_dia,
            'vouchertransacao' => $codigo_voucher,
            'descricao' => 'Inserção Automática',
            'observacao' => 'Comprovativo com Montante pago por Referencia',
            'idcomprovativo' => $comprovativo->id,
            'UtCodigo' => 'dcf',
            'idestado' => 8
        ]);
    }
}