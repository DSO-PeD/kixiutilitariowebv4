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

    public $referencias = 
    [
        
        '002601358',
        '000605646',
        '001203781',
        '001103971',
        '000503816',
        '000405896',
        '002601158',
        '001801006',
        '000903862',
        '001801087',
        '000405712',
        '001005233',
        '002601058',
        '000405541',
        '000205125',
        '001104243',
        '002600928',
        '000205339',
        '000605464',
        '001104267',
        '001104267',
        '000503861',
        '000704965',
        '000704878',
        '002001078',
        '001005404',
        '002601360',
        '000204296',
        '000605478',
        '000204958',
        '001500465',
        '000704982',
        '000903551',
        '002600837',
        '002600837',
        '002600837',
        '001500385',
        '002601140',
        '001500385',
        '000205125',
        '000405541'
    ];

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

    public function actualizarComprovativoRef(){
        //Pega os comprovativos com problemas no comprovativos
        $pagamentos = DB::table('pgtrefnotificacao')
                         ->select(
                            'idLogSistema',
                            'montantePago',
                            'refPagamento',
                            'created_at',
                            DB::raw('RIGHT(refPagamento, 5) as last_five')
                        )
                        //->whereIn('refPagamento', ['000605483'] )
                        ->whereIn('refPagamento', $this->referencias )
                        ->get();

            $cont = 0;
            
            //Para cada pagamento, pega 
            foreach($pagamentos as $pag){
                $dataPagamento = date('Y-m-d H:i',strtotime($pag->created_at));
               
                $comprovativo = DB::table('comprovativos')
                                ->whereRaw("RIGHT(BuDadoOrigem, 5) = ?", [$pag->last_five])
                                ->where('BuMontante','=' ,$pag->montantePago)
                                ->where('UtCodigo','Izipay')
                                //->whereNull('refPagamento')
                                //->where('created_at',$pag->created_at)
                                ->whereRaw(
                                    "DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') = ?",
                                    [$pag->created_at]
                                )
                                ->first();                              
                                
                            /*if(is_object($comprovativo)){
                                    echo 'DATA PAG: '.$pag->created_at.'<br>';
                                    echo 'VALOR PAG: '.$pag->montantePago.'<br>';
                                    echo 'REF PAG: '.$pag->refPagamento.'<br>';
                                    echo 'PERIODO PAG: '.$pag->idLogSistema.'<br>';
                                    echo 'ID_COMP: '.$comprovativo->id.'<br>';
                                    echo 'DATA COMP: '.$comprovativo->CiFecha.'<br>';
                                    echo 'VALOR COMPR: '.$comprovativo->BuMontante.'<br>';
                                    echo '--------------------------'.'<br>';

                                    if(DB::table('comprovativos')
                                        ->where('id',$comprovativo->id)
                                        ->update([
                                            'refPagamento' => $pag->refPagamento,
                                            'periodo_trans_pgr' => $pag->idLogSistema
                                        ])){
                                            $cont++;
                                        }

                            } else {
                                    echo 'NOTOBJ';                                    
                            }*/
                
                
                if(DB::table('comprovativos')
                                ->whereRaw("RIGHT(BuDadoOrigem, 5) = ?", [$pag->last_five])
                                ->where('BuMontante','=' ,$pag->montantePago)
                                ->where('UtCodigo','Izipay')
                                //->whereNull('refPagamento')
                                //->where('created_at',$pag->created_at)
                                ->whereRaw(
                                    "DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') = ?",
                                    [$pag->created_at]
                                )
                                ->update([
                                    'refPagamento' => $pag->refPagamento,
                                    'periodo_trans_pgr' => $pag->idLogSistema
                                ]))
                {
                    $cont++;
                }                        
            }

            echo 'Carregados: '.$cont.' Referências';
    }

    public function actualizarComprovativoRefManual(){
        $referencias = DB::table('referenciasmanuais as r')
                ->join('pgtrefnotificacao as p', 'r.referencia', '=', 'p.refPagamento')
                ->select(
                    'r.BuDadoOrigem',
                    'r.nomecliente',
                    'r.referencia',
                    'r.montante',
                    'p.refPagamento',
                    'p.montantePago',
                    'p.idLogSistema'
                )
                //->where('r.BudadoOrigem','MG/I/01149')
                ->get();

        $ids = $referencias->pluck('BuDadoOrigem')->toArray();

        $caseRefPagamento = "CASE BuDadoOrigem ";
        $casePeriodoTrans = "CASE BuDadoOrigem ";

        foreach ($referencias as $ref) {
            $caseRefPagamento .= "WHEN '{$ref->BuDadoOrigem}' THEN '{$ref->referencia}' ";
            $casePeriodoTrans .= "WHEN '{$ref->BuDadoOrigem}' THEN '{$ref->idLogSistema}' ";
        }

        $caseRefPagamento .= "END";
        $casePeriodoTrans .= "END";

        $cont = 0;

        if(DB::table('comprovativos')
            ->whereIn('BuDadoOrigem', $ids)
            ->update([
                'refPagamento' => DB::raw($caseRefPagamento),
                'periodo_trans_pgr' => DB::raw($casePeriodoTrans)
            ])){
                $cont++;
            };


        dd($cont);

        

        /*foreach($referencias as $ref){

            $cont = 0;

            if(DB::table('comprovativos as c')
                ->where('c.BuDadoOrigem',$ref->BuDadoOrigem)
                ->update([
                    'refPagamento' => $ref->referencia,
                    'periodo_trans_pgr' => $ref->idLogSistema
                ])){
                    $cont++;
                };

            dd($cont);
        }*/
    }
}