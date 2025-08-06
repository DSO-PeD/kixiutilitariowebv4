<?php

namespace App\Http\Controllers;

use App\Models\ComprovativoModel;
use App\Models\CpvtReconciliacaoModel;
use App\Models\PgtRefNotificacaoModel;
use App\Models\TKxExtratoModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PgtRefNotificacaoController extends Controller
{
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
            $codigo_voucher = 'KXU.PGTREF' . $referenciaKIXI;
            try {
                $ExisteReferencia = TKxExtratoModel::where('referenciapagamento', '=', $referenciaKIXI)->first();

                if ($ExisteReferencia) {
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
                        'BuReferencia' => $codigo_voucher,
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
                            'voucher' => $codigo_voucher,
                            'descricao' => 'Inserção Automática',
                            'observacao' => 'Comprovativo com  Montante pago por Referencia',
                            'idcomprovativo' => $insert->id,
                            'UtCodigo' => 'dcf',
                            'idestado' => 8
                        ]);

                    }




                    return response()->json([
                        'success' => true,
                        'Obs' => 'Registro criado com sucesso',
                        'Id' => $item['Id']
                    ]);

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
