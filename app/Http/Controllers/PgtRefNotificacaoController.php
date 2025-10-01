<?php

namespace App\Http\Controllers;

use App\Models\ComprovativoModel;
use App\Models\CpvtReconciliacaoModel;
use App\Models\PgtRefNotificacaoModel;
use App\Models\TKxExtratoModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

    //
    public function carregarPagamentoPorReferencia(Request $request)
    {
        // 1. Validar a access-key
        $accessKey = $request->header('Access-Key') ?? $request->input('access_key');

        if (!$this->validateAccessKey($accessKey)) {
            Log::warning('Tentativa de acesso com chave inválida', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized', 'message' => $request->ip()], 401);
        } else {
            // Captura todos os dados do JSON
            $item = $request->all();


            $apenasNumeros = preg_replace('/\D/', '', $item['refPagamento']);
            $referenciaKIXI = str_pad($apenasNumeros, 9, '0', STR_PAD_LEFT);
            $dataFormatadaREF = Carbon::parse($item['dataTransaccaoCliente'])->format('dmY');

            try {
                $ExisteReferencia = TKxExtratoModel::where('referenciapagamento', '=', $referenciaKIXI)->first();
                $codigo_voucher = 'PREF' . $dataFormatadaREF . '/' . $ExisteReferencia->Lnr;
                $codigo_voucher_dia = 'BMA' . $dataFormatadaREF;


                if ($ExisteReferencia) {

                    $ExisteTransacao = PgtRefNotificacaoModel::where('id', '=', $item['Id'])->first();
                    if ($ExisteTransacao) {
                        return response()->json([
                            'success' => true,
                            'Obs' => 'Já foi processado um pagamento com este ID',
                            'Id' => $item['Id']
                        ], 200);
                    } else {

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
                            'id' => $item['Id']
                        ]);


                        $dataFormatadaBuData = Carbon::parse($item['dataTransaccaoCliente'])->format('Y-m-d');

                        $insert = ComprovativoModel::create([
                            'CiFecha' => now(),
                            'UtCodigo' => 'Izipay',
                            'BaCodigo' => 3,
                            'TtCodigo' => 'DJA',
                            'FormaPago' => 8,
                            'PoCodigo' => 'DJA',
                            'BuDadoOrigem' => $ExisteReferencia->Lnr,
                            'BuReferencia' => $codigo_voucher_dia,
                            'BuReferenciaTransacao' => $codigo_voucher,
                            'BuMontante' => $item['montantePago'],
                            'BuData' => $dataFormatadaBuData,
                            'BuContaBancaria' => '2972939510001',
                            'Eliminado' => 0,
                            'idestado' => 8,
                            'BaseOperacao' => $ExisteReferencia->BaseOperacao,
                            'infoadicional' => $ExisteReferencia->Cliente,
                            'filecomprovativo' => 'Sem extrato',
                            'telefonecliente' => 'Desconhecido'
                        ]);

                        if ($insert) {
                            // Esta inserção serve para reconciliação automática dos comprovativos depósitados, um processo acertado com a DCF
                            $insertReco = CpvtReconciliacaoModel::create([
                                'datareconciliacao' => now(),
                                'CodigoConta' => 79,
                                'voucher' => $codigo_voucher_dia,
                                'vouchertransacao' => $codigo_voucher,
                                'descricao' => 'Inserção Automática',
                                'observacao' => 'Comprovativo com  Montante pago por Referencia',
                                'idcomprovativo' => $insert->id,
                                'UtCodigo' => 'dcf',
                                'idestado' => 8
                            ]);

                        }

                        if ($registro) {
                            $validKey = config('djanotifpgtref.callback_access_key');

                            $comprovativo = ComprovativoModel::where('BuDadoOrigem', '=', $ExisteReferencia->Lnr)->first();

                            $telefone = null;
                             $mensagem = "Pagamento recebido\n".
                                        "Kz ".number_format($item['montantePago'], 2, ',', '.')."\n".
                                        "Empréstimo número {$ExisteReferencia->Lnr}\n\n".
                                        "KIXICREDITO\n".
                                        "PARCEIRA NOS NEGÓCIOS";
                            if ($ExisteReferencia->Telefone) {
                                $telefone = $ExisteReferencia->Telefone;
                            } else if ($comprovativo->teletelefonecliente) {
                                $telefone = $comprovativo->teletelefonecliente;
                            }

                            if ($telefone) {
                                $response = Http::withHeaders([
                                    'Access-Key' => $validKey,
                                    'Content-Type' => 'application/json',
                                ])->post('https://kixisms.kixicredito.com/api/enviarSMS', [
                                            'contacto' => $telefone,
                                            'mensagem' => $mensagem,
                                        ]);


                            }
                            Log::info('Tentativa de envio SMS', ['telefone' => $telefone, 'mensagem ' => $mensagem, 'montante' => $item['montantePago']]);
                        }

                        return response()->json([
                            'success' => true,
                            'Obs' => 'Registro criado com sucesso',
                            'Id' => $item['Id']
                        ], 200);

                    }









                } else {


                    return response()->json([
                        'success' => false,
                        'Obs' => 'A referência de pagamento não existe',
                        'Id' => $item['Id']
                    ]);


                }
            } catch (\Exception $e) {



                return response()->json([
                    'success' => false,
                    'Obs' => 'Erro ao processar o item',
                    'Id' => $item['Id'],
                    'error' => 'Contactar da DSO'
                ]);


            }








        }



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
            ])->post('https://kixisms.kixicredito.com/api/enviarSMS', [
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


}
