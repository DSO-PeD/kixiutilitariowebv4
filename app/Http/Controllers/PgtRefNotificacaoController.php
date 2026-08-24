<?php

namespace App\Http\Controllers;

use App\Models\ComprovativoModel;
use App\Models\CpvtReconciliacaoModel;
use App\Models\HelperModel;
use App\Models\PgtRefNotificacaoModel;
use App\Models\ReferenciaPGTModel;
use App\Models\TKxClProdutoModel;
use App\Models\TKxExtratoModel;
use App\Models\VoucherHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PgtRefNotificacaoController extends Controller
{

    // Função que remove tudo que não for número
    public function limparTelefone($telefone)
    {
        return preg_replace('/\D/', '', $telefone);
    }

    // Função que valida se o telefone tem exatamente 9 dígitos
    public function telefoneValido($telefone)
    {
        $telefone = $this->limparTelefone($telefone);
        return preg_match('/^\d{9}$/', $telefone) ? $telefone : null;
    }

    /*******
    idTransacao	
    numLogSistema	
    idLogSistema	
    dataTransaccaoCliente	
    montantePago	
    tipoTerminal	
    iIdentTerminal	
    localidadeTerminal	
    refPagamento	
    Id	
    nib	
    banco
    *******/	

    public function carregarPagamentoPorReferencia(Request $request)
    { 
        // 1. Validar a access-key
        $accessKey = $request->header('Access-Key') ?? $request->input('access_key');

        if (!$this->validateAccessKey($accessKey)) {
            Log::warning('Tentativa de acesso com chave inválida', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized', 'message' => $request->ip()], 401);
        }

        // Captura todos os dados do JSON
        $item = $request->all();

        DB::beginTransaction();

        try {
            $apenasNumeros = preg_replace('/\D/', '', $item['refPagamento']);
            $referenciaKIXI = str_pad($apenasNumeros, 9, '0', STR_PAD_LEFT);
            $dataFormatadaREF = Carbon::parse($item['dataTransaccaoCliente'])->format('dmY');
            $dataHoraFormatadaREF = Carbon::parse($item['dataTransaccaoCliente'])->format('dmY His');
        
            // Verificar se transação já existe
            $ExisteTransacao = PgtRefNotificacaoModel::where('id', '=', $item['Id'])->first();
            if ($ExisteTransacao) {
                return response()->json([
                    'success' => true,
                    'Obs' => 'Já foi processado um pagamento com este ID',
                    'Id' => $ExisteTransacao->IDKixiRegister,
                ], 200);
            } 

            // Buscar referência
            $ExisteReferencia = $this->buscarReferencia($referenciaKIXI);
            if (!$ExisteReferencia['encontrada']) {
                return response()->json([
                    'success' => false,
                    'Obs' => 'A referência de pagamento não existe',
                    'Id' => $ExisteTransacao->IDKixiRegister,
                ], 404);
            }
                                        
            // Processar pagamento
            $this->processarPagamento($item, $ExisteReferencia, $referenciaKIXI, $dataFormatadaREF,$dataHoraFormatadaREF);

            DB::commit();

            return response()->json([
                'success' => true,
                'Obs' => 'Registro criado com sucesso',
                'Id' => $item['Id']
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao processar pagamento por referência', [
                'id' => $item['Id'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'Obs' => 'Erro ao processar o item',
                'Id' => $item['Id'] ?? 'unknown',
                'error' => 'Contactar a DSO'
            ], 500);
        }
    }

    // Métodos auxiliares
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
        $referenciaManual = ReferenciaPGTModel::where('referencia', $referenciaKIXI)
                                                ->select(
                                                    'id',
                                                    'BuDadoOrigem',
                                                    'nomecliente',
                                                    'telefone as Telefone',
                                                    'PoCodigo',
                                                    'tipo',
                                                    'referencia',
                                                    'inicio',
                                                    'fim',
                                                    'montante',
                                                    'montantepago',
                                                    'activo',
                                                    'idestado',
                                                    'BaseOperacao',
                                                    'UtCodigo',
                                                    'updated_at',
                                                    'created_at'
                                                )
                                                ->first();

        if ($referenciaManual) {
            return [
                'encontrada' => true,
                'dados' => $referenciaManual,
                'lnr' => $referenciaManual->BuDadoOrigem,
                'metodologia' => $referenciaManual->PoCodigo ?? 'DJA',
                'telefone' => $produto->telefone ?? 'Desconhecido',
                'tipo' => 'manual'
            ];
        }

        return ['encontrada' => false];
    }

    private function processarPagamento(array $item, array $dadosReferencia, string $referenciaKIXI, string $dataFormatadaREF, string $dataHoraFormatadaREF)
    {  
        // Criar registro de notificação
        $registro = PgtRefNotificacaoModel::create([
            'idTransacao' => $item['idTransacao'],
            'numLogSistema' => $item['numLogSistema'],
            'idLogSistema' => $item['idLogSistema'],
            'dataTransaccaoCliente' => $item['dataTransaccaoCliente'],
            'montantePago' => $item['montantePago'],
            'tipoTerminal' => $item['tipoTerminal'],
            'iIdentTerminal' => $item['iIdentTerminal'],
            'localidadeTerminal' => $item['localidadeTerminal'],
            'refPagamento' => $referenciaKIXI,
            'Id' => $item['Id'],
            'nib' => $item['nib'],
            'banco' => $item['banco']
        ]);

        if ($registro) {
            // Se for referência manual, atualizar os campos
            if ($dadosReferencia['tipo'] === 'manual') {
                // Calcular o montante total já pago para esta referência
                $valorPago = PgtRefNotificacaoModel::where('refPagamento', $referenciaKIXI)->sum('montantePago');
                $this->atualizarReferenciaManual($dadosReferencia['dados']->id, $valorPago);
            }
            
            $this->criarComprovativoEReconciliacao($item, $dadosReferencia, $dataFormatadaREF, $dataHoraFormatadaREF, $referenciaKIXI);
        }
    }

    private function criarComprovativoEReconciliacao(array $item, array $dadosReferencia, string $dataFormatadaREF, string $dataHoraFormatadaREF, string $referenciaKIXI)
    { 
        $dataFormatadaBuData = Carbon::parse($item['dataTransaccaoCliente'])->format('Y-m-d H:i:s');

        /** Formar VOucher do dia */
        /** FORMATO: BMADDMMYYYYBASESIGLANRHORA = BMA30032026AC12345005959 */
        $data = substr($dataHoraFormatadaREF, 0, 8);
        $hora = substr($dataHoraFormatadaREF, 9, 6);
        $lnr = str_replace('/', '', $dadosReferencia['lnr']); 
        
        $codigo_voucher_dia = 'BMA' . $data . $lnr . $hora;
        $codigo_voucher_dia = VoucherHelper::criptografar(VoucherHelper::parseVoucher($codigo_voucher_dia));
        
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
            'BuMontante' => $item['montantePago'],
            'BuData' => $dataFormatadaBuData,
            'BuContaBancaria' => '2972939510001',
            'Eliminado' => 0,
            'idestado' => 8,
            'BaseOperacao' => $dadosReferencia['dados']->BaseOperacao,
            'infoadicional' => $dadosReferencia['dados']->Cliente ?? $dadosReferencia['dados']->nomecliente ?? 'Desconhecido',
            'filecomprovativo' => 'Sem extrato',
            'telefonecliente' => $dadosReferencia['dados']->Telefone,
            'periodo_trans_pgr' => $item['idLogSistema'],
            'refPagamento' => $referenciaKIXI
        ]);

        if ($comprovativo) {
            $this->criarReconciliacao($comprovativo, $codigo_voucher_dia, $codigo_voucher, $dataFormatadaBuData);
            $this->enviarNotificacaoSMS($item, $dadosReferencia);
            
            //Calcular capital e juros
            if($dadosReferencia['tipo'] === 'extrato'){
                $status = HelperModel::calcularCapitalEJuros($comprovativo->BuMontante,$comprovativo->BuDadoOrigem,$comprovativo->id);
            }
        }
    }

    private function criarReconciliacao($comprovativo, string $codigo_voucher_dia, string $codigo_voucher, $data): void
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

    private function enviarNotificacaoSMS(array $item, array $dadosReferencia): void
    {
        $telefone = $this->obterTelefoneCliente($dadosReferencia);

        if (!$telefone) {
            Log::info('Não foi possível enviar SMS: telefone não encontrado');
            return;
        }

        $mensagem = $this->construirMensagemSMS($item, $dadosReferencia);
        $validKey = config('djanotifpgtref.callback_access_key');

        try {
            $response = Http::withHeaders([
                'Access-Key' => $validKey,
                'Content-Type' => 'application/json',
            ])->post('http://kixisms.kixicredito.com/api/enviarSMS', [
                        'contacto' => $telefone,
                        'mensagem' => $mensagem,
                    ]);

            Log::info('SMS enviado com sucesso', [
                'telefone' => $telefone,
                'montante' => $item['montantePago']
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao enviar SMS', [
                'telefone' => $telefone,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function obterTelefoneCliente(array $dadosReferencia): ?string
    {
        // Buscar telefone considerando possíveis nomes de colunas diferentes
        $telefone = $dadosReferencia['dados']->Telefone ?? $dadosReferencia['dados']->telefone ?? null;

        if (!$telefone) {
            $comprovativo = ComprovativoModel::where('BuDadoOrigem', $dadosReferencia['lnr'])->first();
            $telefone = $comprovativo->telefonecliente ?? null;
        }

        return $telefone;
    }

    private function construirMensagemSMS(array $item, array $dadosReferencia): string
    {
        $montanteFormatado = number_format($item['montantePago'], 2, ',', '.');

        return "Pagamento recebido\n" .
            "Kz " . $montanteFormatado . "\n" .
            "Empréstimo número " . $dadosReferencia['lnr'] . "\n\n" .
            "KIXICREDITO\n" .
            "PARCEIRA NOS NEGÓCIOS";
    }

    public function sendSms()
    {

        $validKey = config('djanotifpgtref.callback_access_key');
        $ExisteReferencia = TKxExtratoModel::where('referenciapagamento', '=', '000100501')->first();

        $comprovativo = ComprovativoModel::where('BuDadoOrigem', '=', 'HO/00501')->first();

        $telefone = "921502056";
        $mensagem = "Olá, Scripto seu pagamento no valor de 20.000 Kz foi realizado com sucesso. \n KixiCrédito, parceira nos negócios.";

        if ($ExisteReferencia->Telefone) {
            $telefone = $ExisteReferencia->Telefone;
        } else if ($comprovativo->teletelefonecliente) {
            $telefone = $comprovativo->teletelefonecliente;
        }

        if ($telefone) {
            $response = Http::withHeaders([
                'Access-Key' => $validKey,
                'Content-Type' => 'application/json',
            ])->post('http://kixisms.kixicredito.com/api/enviarSMS', [
                        'contacto' => $telefone,
                        'mensagem' => $mensagem,
                    ]);

            Log::info('Tentativa de acesso com chave inválida', ['telefone' => $telefone, 'mensagem ' => $mensagem]);
        }

    }

    protected function validateAccessKey($accessKey)
    {
        /*
            php artisan tinker
            >>> echo bin2hex(random_bytes(32));
        */
        $validKey = config('djanotifpgtref.callback_access_key');

        return !empty($validKey) && hash_equals($validKey, $accessKey);
    }

    public function criptografarVoucher($voucher){
        //$voucher = "BMA30032026MG01266235959";
        
        // Encryptar
        $codigoEncriptado = VoucherHelper::criptografar(VoucherHelper::parseVoucher($voucher));
        echo "Criptografado: $codigoEncriptado\n <br>";
        
        // Decryptar
        $dados = VoucherHelper::descriptografar($codigoEncriptado);
        $voucherOriginal = VoucherHelper::montarVoucher($dados);

        echo "Voucher original: $voucherOriginal\n <br>";
    }

    public function descriptografarVoucher($voucher){
        //$voucher = "dDXDwnxt7";
        
        // Encryptar
        echo "== Voucher Criptografado ==: $voucher <br>";
        
        // Decryptar
        $dados = VoucherHelper::descriptografar($voucher);
        $voucherOriginal = VoucherHelper::montarVoucher($dados);

        echo "== Voucher Original ==: $voucherOriginal\n";
    }
}
