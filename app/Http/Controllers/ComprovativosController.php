<?php

namespace App\Http\Controllers;

use App\Models\ComprovativoModel;
use App\Models\CpvtReconciliacaoModel;
use App\Models\EstadosModel;
use App\Models\RecuperacaoModel;
use App\Models\TKuPendentesModel;
use App\Models\TKxAgenciaModel;
use App\Models\TKxBancoContaModel;
use App\Models\TKxBancoModel;
use App\Models\TKxClProdutoModel;
use App\Models\TKxClTipopagamentoModel;
use App\Models\TKxUsUtilizadorModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;

use function Pest\Laravel\get;

class ComprovativosController extends Controller
{
    public function viewComprovativos(Request $request)
    {
        $authenticatedUser = Auth::user();

        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $authenticatedUser->UtAgencia)->first();

        //  $filtros = $request->only(['search', 'data_inicio', 'data_fim']);


        $tipoDeBusca = $request->tipo;
        $tipoProdutoPP = $request->filtrar_poupancas;
        $tipoProdutoPT = $request->filtrar_prestacoes;


        $produto_poupancas = "";
        $produto_prestacoes = "";
        $lista_produtos = TKxClProdutoModel::getProdutos();
        $lista_das_formaspagamento = TKxClTipopagamentoModel::getFormasDePamentos();



        $NumeroRegistroTabela = $resultagencia_user->NumeroRegistroTabela;
        $dataFecho = $resultagencia_user->DataFecho;

        $dataFecho = date("Y-m-d", strtotime($dataFecho));
        $hoje = date('Y-m-d');
        $ConsultaBasesConsulta = "'" . $resultagencia_user->BasesOperacao . "'";
        $dataActual = date("Y-m-d", strtotime($hoje));
        $sistema_aberto = true;

        $estados = EstadosModel::getEstadosDCF('DCF');
        $ids_estados = $estados->pluck('id')->implode(',');

        $produto_poupancas_busca = collect($lista_produtos)->where('TipoProduto', '=', 'S');
        $produto_poupancas_busca = "'" . $produto_poupancas_busca->pluck('Metodologia')->implode(',') . "'";


        $produto_prestacoes_busca = collect($lista_produtos)->where('TipoProduto', '=', 'L');
        $produto_prestacoes_busca = "'" . $produto_prestacoes_busca->pluck('Metodologia')->implode(',') . "'";

        $produtos_geral_busca = "'" . $lista_produtos->pluck('Metodologia')->implode(',') . "'";
        $formaspagamento_geral = "'" . $lista_das_formaspagamento->pluck('FormaPago')->implode(',') . "'";


        if ($dataFecho == $dataActual) {
            $sistema_aberto = false;
        } else {
            $sistema_aberto = true;
        }

        $Bases = "'" . $resultagencia_user->BasesOperacao . "'";


        $ESTADO = "'" . $ids_estados . "'";
        $DataInicio = date("Y-m-d 00:00:00", strtotime('-7 day', strtotime($hoje)));
        $DataFim = date("Y-m-d 23:59:00", strtotime($hoje));
        $TIPO = 0;
        $LOAN = "'DS/280890'";

        $BasesOperacao = explode(',', $resultagencia_user->BasesOperacao);



        if ($tipoDeBusca == 1) {
            $DataInicio = date("Y-m-d 00:00:00", strtotime($request->data_inicio));
            $DataFim = date("Y-m-d 23:59:00", strtotime($request->data_fim));
            $TIPO = $tipoDeBusca;

            //dd( $DataFim );
        }

        if ($tipoDeBusca == 3) {
            $LOAN = "'" . $request->loan . "'";
            $TIPO = $tipoDeBusca;
        }
        if ($tipoDeBusca == 4) {

            $DataInicio = date("Y-m-d 00:00:00", strtotime($request->data_inicio_imput));
            $DataFim = date("Y-m-d 23:59:00", strtotime($request->data_fim_imput));

            if ($request->estado_input !== '28') {
                $ESTADO = $request->estado_input;
                // dd($request->estado_input);
            }
            if ($request->agencia_imput !== 'T') {
                $Bases = "'" . $request->agencia_imput . "'";
            }

            if ($tipoProdutoPT && !$tipoProdutoPP) {
                if ($request->produto_prestacao !== 'TL') {
                    $produto_prestacoes_busca = "'" . $request->produto_prestacao . "'";

                }
                $produtos_geral_busca = $produto_prestacoes_busca;
            }

            if ($tipoProdutoPP && !$tipoProdutoPT) {
                if ($request->produto_poupanca !== 'TS') {
                    $produto_poupancas_busca = "'" . $request->produto_poupanca . "'";
                }
                $produtos_geral_busca = $produto_poupancas_busca;
            }
            if ($request->forma_pagamento !== 'TP') {
                $formaspagamento_geral = "'" . $request->forma_pagamento . "'";
            }

            $TIPO = $tipoDeBusca;
        }



        $lista_comprovativo = ComprovativoModel::getComprovativos($Bases, $DataInicio, $DataFim, $NumeroRegistroTabela, $TIPO, $LOAN, $ESTADO, $produtos_geral_busca, $formaspagamento_geral);


        $lista_banco = TKxBancoModel::getBancos();
        $lista_bancos_contas = TKxBancoContaModel::getBancosContas();

        $estados = EstadosModel::getEstadosDCF('DCF');
        $BasesOperacaoAgencias = TKxAgenciaModel::whereIn('OfIdentificador', $BasesOperacao)->get();
        $total = sizeof($lista_comprovativo);

        $totalMontante = collect($lista_comprovativo)->where('TtCodigo', '=', 'L04')->sum('BuMontante');
        $totalMontantePoupanca = collect($lista_comprovativo)->where('TtCodigo', '=', 'S01')->sum('BuMontante');
        $DataInicio = collect($lista_comprovativo)->max('CiFecha');
        $DataFim = collect($lista_comprovativo)->min('CiFecha');

        $DataInicioFormatada = Carbon::parse($DataInicio)->format('d/m/Y');
        $DataFimFormatada = Carbon::parse($DataFim)->format('d/m/Y');



        $lista_pendentes = TKuPendentesModel::whereIn('BaseOperacao', $BasesOperacao)->where('Tipo', 'R')->get();
        //dd($lista_pendentes->count());
        $TipoComprovativo = [
            'G' => 'G/',
            'I' => 'I/'
        ];
        // dd($lista_comprovativo );
        $comprovativos_list = collect($lista_comprovativo)->map(function ($item) {

            return [
                'id' => $item->id,
                'data' => $item->dataRegistoFomatada,
                'agencia' => $item->OfNombre,
                'basedelacamento' => $item->basedelacamento,
                'file' => $item->filecomprovativo,
                'usuario' => $item->UtNome,
                'lnr' => $item->BuDadoOrigem,
                'estado' => $item->estado,
                'color' => $item->color,
                'cliente' => $item->infoadicional,
                'observacao' => $item->observacao,
                'metodologia' => $item->PoAgrupado,
                'banco' => $item->BaSigla,
                'conta' => $item->ContaBacaria,
                'referencia' => $item->BuReferencia,
                'voucher' => $item->voucher,
                'FormaPagoN' => $item->FormaPagoN,
                'descricao' => $item->descricao,
                'operadordcf' => $item->operadordcf,
                'datareconciliacao' => $item->datareconciliacao,
                'montante' => $item->BuMontante,
                // Mantenha todos os campos necessários para filtros client-side
                'CiFecha' => $item->CiFecha, // Para filtro por data
                'estado_id' => $item->idestado, // Para filtro por estado
                'OfIdentificador' => $item->OfIdentificador, // Para filtro por agência
                'BuMontante' => $item->BuMontante // Para cálculos

            ];
        });



        $NumeroPaginator = 30;
        //  $paginado = $comprovativos_list->forPage(page: $request->input('page', 1), $NumeroPaginator)->values();
        return Inertia::render('Comprovativos', [
            'lista_comprovativo' => $comprovativos_list,
            // 'comprovativos' => $paginado,
            'filters' => [
                'search' => $request->input('search_input', ''),
                'lnr' => $request->input('lnr_imput', ''),
                'estado' => $request->input('estado_input', 28), // Valor padrão 28 (Todos)
                'agencia' => $request->input('agencia_imput', default: 'T'), // Valor padrão 'T' (Todas)
                'formaPagamento' => $request->input('forma_pagamento', 'TP'), // Valor padrão 'T' (Todas)
                'produtoPrestacao' => $request->input('produto_prestacao', 'TL'),
                'produtoPoupanca' => $request->input('produto_poupanca', 'TS'),
                'data_inicio' => $request->input('data_inicio_imput', ''),
                'data_fim' => $request->input('data_fim_imput', ''),
                'filtrar_prestacoes' => (bool) $request->input('filtrar_prestacoes', true),
                'filtrar_poupancas' => (bool) $request->input('filtrar_poupancas', true),
            ],
            'page' => (int) $request->input('page', 1),
            'bases' => $BasesOperacaoAgencias,
            'produtos' => $lista_produtos,
            'bancos' => $lista_banco,
            'contas' => $lista_bancos_contas,
            'tipocomprovativos' => $TipoComprovativo,
            'estados' => $estados,
            //  'lista_comprovativo' => $lista_comprovativo,
            'total' => $total,
            'montantetotal' => $totalMontante,
            'totalMontantePoupanca' => $totalMontantePoupanca,
            'formaspagamentos' => $lista_das_formaspagamento,
            //'hasMorePages' => $comprovativos_list->count() > $request->input('page', 1) * $NumeroPaginator,
            'lista_pendentes' => $lista_pendentes,
            'totalPendente' => $lista_pendentes->count(),
            'dataInicioPeriodo' => $DataInicioFormatada,
            'dataFimPeriodo' => $DataFimFormatada,

        ]);
    }


    public function guardar(Request $request)
    {
        $authenticatedUser = Auth::user();

        $cadastrar_o_que = $request->ls;
        $hoje = date('Y-m-d H:i:s');
        $dataFinal = date('Y-m-d');
        $Mensagem = "";
        $pathArquivo = "";

        $dataFormatadaBuData = Carbon::createFromFormat('d/m/Y', $request->calDataBorderoux)->format('Y-m-d');

        $dias_passado = $this->diasDatas($dataFormatadaBuData, $dataFinal);


        $NumeroDiaNecessario = 90;//$request->DiasMaximoRegistroComprovativo;

        $loan_number_v = "";

        $verificar_ = ComprovativoModel::verificarSeBorderouxExiste($request->txtVoucher, $request->calDataBorderoux, $request->selectBanco, 0);

        if ($verificar_) {

            $Mensagem = " Ups!, Já existe um comprovativo com o voucher indicado: [   Loan Number: " . $verificar_->BuDadoOrigem . " | Voucher: " . $verificar_->BuReferencia . " | Montante: " . $verificar_->BuMontante . " | Data do Borderoux: " . $verificar_->BuData . " ] => Por favor contactar a DCF para  esclarecer esta situação.";

            return redirect()
                ->back()
                ->with('error', $Mensagem);
        } else {
            if (false) {
                return redirect()
                    ->back()
                    ->with('error', 'Ups!,  Comprovativo Muito antigo, excedeu o número de dias permitido  para ser cadastrado!, infelizmente não foi cadastrado.');
            } else {

                $request->validate([

                    'anexo' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
                ]);

                $estadoRegistado = 1;
                $comprovativonome = "";
                if ($request->hasFile('anexo')) {
                    $pathArquivo = $request->file('anexo')->store('comprovativos', 'public');

                    // Corrigido: Obtém apenas o nome do arquivo salvo
                    $comprovativonome = basename($pathArquivo);
                    // Salve $path no banco de dados
                }
                $_conta = 0;

                if ($cadastrar_o_que == "Loan") {

                    $siglabase = $request->selectBase;
                    $numeroLoanSanving = $request->txtNumeroLoanSaving;
                    $produto = $request->selectProdutoLoan;
                    $infoadicional = $request->txtInfoAdicional;
                    $formapgt = $request->selectFormaPagamento;
                    $telefone = $request->telefone;

                    $tipotransacao = "L04";
                    $banco = $request->banco;

                    $money = $request->txtMontante;
                    $dataBorderoux = $dataFormatadaBuData;
                    $voucher = $request->txtVoucher;
                    $contaBancaria = $request->conta;

                    $loan_number = $siglabase;
                    $loan_number .= "/";
                    $loan_number .= $numeroLoanSanving;

                    // $_verifica_esta_aplicado_no_extrato = TKxextrato::verificarSeExtratoExiste($loan_number, $siglabase);
                    // dd($contaBancaria);
                    //  if (true) {
                    if ($formapgt == 14) {
                        $estadoRegistado = 8;
                        $_conta = TKxBancoContaModel::where('codigoConta', $request->conta)->first();
                        $contaBancaria = $_conta->ContaBacaria;
                        $voucher = $request->txtVoucher;
                    }



                    $insert = ComprovativoModel::create([
                        'CiFecha' => now(),
                        'UtCodigo' => $authenticatedUser->UtCodigo,
                        'BaCodigo' => $banco,
                        'TtCodigo' => $tipotransacao,
                        'FormaPago' => $formapgt,
                        'PoCodigo' => $produto,
                        'BuDadoOrigem' => $loan_number,
                        'BuReferencia' => $voucher,
                        'BuMontante' => $money,
                        'BuData' => $dataFormatadaBuData,
                        'BuContaBancaria' => $contaBancaria,
                        'Eliminado' => 0,
                        'idestado' => $estadoRegistado,
                        'BaseOperacao' => $siglabase,
                        'infoadicional' => $infoadicional,
                        'filecomprovativo' => $comprovativonome,
                        'telefonecliente' => $telefone
                    ]);
                    //} else {

                    // $Mensagem = "Comprovativo não foi cadastrado!, verificamos que não existe qualquer aplicação no Kixi Utiltário, do crédito: [" . $loan_number . "],  por favor faça primeiro  a aplicação em < Cálculo de Desembolso > depois tenta novamente cadastrar este comprovativo.";
                    //   return redirect()->back()->with('error', $Mensagem);
                    // }

                } else {

                    $formapgt = $request->selectFormaPagamento;
                    $siglabase = $request->selectBase;
                    $siglaIndividualgrupal = $request->selectGrupoIndividual;
                    $siglaIndividualgrupal .= "/";
                    $numeroLoanSanving = $request->txtNumeroLoanSaving;
                    $produto = $request->selectProdutoSaving;
                    $tipotransacao = "S01";
                    $banco = $request->banco;
                    $money = str_replace('.', '', $request->txtMontante);
                    $money = str_replace(",", ".", $money);
                    $dataBorderoux = $dataFormatadaBuData;
                    $voucher = null;
                    $contaBancaria = $request->conta;
                    $infoadicional = $request->txtInfoAdicional;
                    $telefone = $request->telefone;
                    $loan_number = $siglabase;
                    $loan_number .= "/";
                    $loan_number .= $siglaIndividualgrupal;
                    $loan_number .= $numeroLoanSanving;


                    if ($formapgt == 14) {
                        $estadoRegistado = 8;
                        $_conta = TKxBancoContaModel::where('codigoConta', $request->conta)->first();
                        $contaBancaria = $_conta->ContaBacaria;
                        $voucher = $request->txtVoucher;
                    }


                    // OUTRA FORMA DE CAPTURAR DADOS DO FORMULÁRIO E ARMAZENAR NA BASE DE DADOS
                    $insert = ComprovativoModel::create([
                        'CiFecha' => now(),
                        'UtCodigo' => $authenticatedUser->UtCodigo,
                        'BaCodigo' => $banco,
                        'TtCodigo' => $tipotransacao,
                        'FormaPago' => 1,
                        'PoCodigo' => $produto,
                        'BuDadoOrigem' => $loan_number,
                        'BuReferencia' => $voucher,
                        'BuMontante' => $money,
                        'BuData' => $dataFormatadaBuData,
                        'BuContaBancaria' => $contaBancaria,
                        'Eliminado' => 0,
                        'idestado' => $estadoRegistado,
                        'BaseOperacao' => $siglabase,
                        'infoadicional' => $infoadicional,
                        'telefonecliente' => $telefone,
                        'filecomprovativo' => $comprovativonome,

                    ]);
                }

                if ($insert) {


                    if ($formapgt == 14) {
                        $voucher = $request->txtVoucher;
                        // Esta inserção serve para reconciliação automática dos comprovativos depósitados, um processo acertado com a DCF
                        $insertReco = CpvtReconciliacaoModel::create([
                            'datareconciliacao' => now(),
                            'CodigoConta' => $request->conta,
                            'voucher' => $voucher,
                            'descricao' => 'Inserção Automática',
                            'observacao' => 'Comprovativo com  Montante Despósitado',
                            'idcomprovativo' => $insert->id,
                            'UtCodigo' => 'dcf',
                            'idestado' => $estadoRegistado
                        ]);
                    }
                    return Redirect::route('comprovativos')
                        ->with('success', 'Dados guardados com sucesso!');
                } else {
                    return Redirect::back()
                        ->with('error', 'Ups!, algo correu errado ao cadastrar comprovativo!');
                }
            }
        }
    }

    public function finalizaraeliminacao(Request $request)
    {

        $hoje = date('d/m/Y');
        $Mensagem = "";
        $authenticatedUser = Auth::user();


        // Eliminação para utilizadores MASTERS cuidado
        if ($authenticatedUser->elimina_confirmado_exportado) {


            $ERASER = ComprovativoModel::setEliminarComprovativoMASTER($request->id);

            if ($ERASER) {
                return back()->with('success', 'Comprovativo eliminado com  sucesso!');
            } else {
                return back()->with('error', 'Ups! algo aconteceu errado  ao eliminar este comprovativo, por favor cotactar o P&D');
            }



        } else {

            //Eliminação para utilizadores mini

            $verica_existe_recupercao = RecuperacaoModel::where('id_comprovativo', $request->id)->first();
            $verica_existe_reconciliacao = CpvtReconciliacaoModel::where('idcomprovativo', $request->id)->first();

            if ($verica_existe_recupercao) {
                $Mensagem = " Ups!, O comprovativo não pode ser eliminado porque esta associado a uma recuperação! [   Loan Number: " . $verica_existe_recupercao->ReBuDadoOrigem . " | Voucher: " . $verica_existe_recupercao->ReBuReferencia . " | Montante: " . $verica_existe_recupercao->ReBuMontante . " | Cod. Recuperador: " . $verica_existe_recupercao->id_recuperador . " ] => Por favor contactar a DSO para  esclarecer esta situação.";
                return back()->with('error', $Mensagem);
            } elseif ($verica_existe_reconciliacao) {
                $Mensagem = " Ups!, O comprovativo não pode ser eliminado porque já foi feito a reconciliação! [   Voucher: " . $verica_existe_reconciliacao->voucher . " | Data de reconciliacao: " . $verica_existe_reconciliacao->datareconciliacao . "  | Cod. Reconciliador: " . $verica_existe_reconciliacao->UtCodigo . " ] => Por favor contactar a DSO para  esclarecer esta situação.";
                return back()->with('error', $Mensagem);
            } else {

                $updated = ComprovativoModel::where('id', $request->id)
                    ->update([
                        'Eliminado' => 1,
                        'Motivo' => "Detalhe no Kixi Agenda",
                        'UtCodigoEliminou' => $authenticatedUser->UtCodigo,
                        'DataEliminacao' => now()
                    ]);

                if ($updated) {
                    return back()->with('success', 'Comprovativo eliminado com  sucesso!');
                } else {
                    return back()->with('error', 'Ups! algo aconteceu errado  ao eliminar este comprovativo, por favor cotactar a DSO');
                }
            }


        }

    }
    public static function diasDatas($data_inicial = '2013-08-01', $data_final = '2013-08-16')
    {
        $diferenca = strtotime($data_final) - strtotime($data_inicial);
        $dias = floor($diferenca / (60 * 60 * 24));
        return $dias;
    }


    public function carregaComprovativosKP(Request $request)
    {

        $DataInicio = date("Y-m-d 00:00:00", strtotime($request->data_inicio));


        $compravativos = ComprovativoModel::whereDate('CiFecha', '>=', $DataInicio)->where('Eliminado', 0)->get();

        return response()->json($compravativos);

    }





}
