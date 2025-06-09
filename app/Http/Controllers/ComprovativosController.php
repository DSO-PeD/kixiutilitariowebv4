<?php

namespace App\Http\Controllers;

use App\Models\ComprovativoModel;
use App\Models\EstadosModel;
use App\Models\RecuperacaoModel;
use App\Models\TKxAgenciaModel;
use App\Models\TKxBancoContaModel;
use App\Models\TKxBancoModel;
use App\Models\TKxClProdutoModel;
use App\Models\TKxClTipopagamentoModel;
use App\Models\TKxUsUtilizadorModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ComprovativosController extends Controller
{
    public function viewComprovativos(Request $request)
    {
        $authenticatedUser = Auth::user();

        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $authenticatedUser->UtAgencia)->first();

        $filtros = $request->only(['search', 'data_inicio', 'data_fim']);


        $tipoDeBusca = $request->tipo;


        $NumeroRegistroTabela = $resultagencia_user->NumeroRegistroTabela;
        $dataFecho = $resultagencia_user->DataFecho;

        $dataFecho = date("Y-m-d", strtotime($dataFecho));
        $hoje = date('Y-m-d');
        $ConsultaBasesConsulta = "'" . $resultagencia_user->BasesOperacao . "'";
        $dataActual = date("Y-m-d", strtotime($hoje));
        $sistema_aberto = true;

         $estados = EstadosModel::getEstadosDCF('DCF');
        $ids_estados = $estados->pluck('id')->implode(',');


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
            $DataInicio =date("Y-m-d 00:00:00", strtotime($request->data_inicio));
            $DataFim = date("Y-m-d 23:59:00", strtotime($request->data_fim));
            $TIPO = $tipoDeBusca;

            //dd( $DataFim );
        }

        if ($tipoDeBusca == 3) {
            $LOAN = "'" . $request->loan . "'";
            $TIPO = $tipoDeBusca;
        }


        $lista_comprovativo = ComprovativoModel::getComprovativos($Bases, $DataInicio, $DataFim, $NumeroRegistroTabela, $TIPO, $LOAN, $ESTADO);

        $lista_produtos = TKxClProdutoModel::getProdutos();
        $lista_banco = TKxBancoModel::getBancos();
        $lista_bancos_contas = TKxBancoContaModel::getBancosContas();
        $lista_das_formaspagamento = TKxClTipopagamentoModel::getFormasDePamentos();
        $estados=EstadosModel::getEstadosDCF('DCF');
        $BasesOperacaoAgencias = TKxAgenciaModel::whereIn('OfIdentificador', $BasesOperacao)->get();
        $TipoComprovativo = [
            'G' => 'G/',
            'I' => 'I/'
        ];

        $comprovativos_list = collect($lista_comprovativo)->map(function ($item) {
            return [
                'id' => $item->id,
                'data' => $item->dataRegistoFomatada,
                'file' => $item->filecomprovativo,
                'usuario' => $item->UtNome,
                'lnr' => $item->BuDadoOrigem,
                'estado' => $item->estado,
                'color' => $item->color,
                'observacao' => $item->observacao,
                'metodologia' => $item->PoAgrupado,
                'referencia' => $item->BuReferencia,
                'montante' => number_format($item->BuMontante, 2, ',', '.'),
                'cliente' => $item->infoadicional
            ];
        });


        $NumeroPaginator = 30;
        $paginado = $comprovativos_list->forPage($request->input('page', 1), $NumeroPaginator)->values();

        return Inertia::render('Comprovativos', [
            'comprovativos' => $paginado,
            'filters' => $filtros,
            'page' => (int) $request->input('page', 1),
            'bases' => $BasesOperacaoAgencias,
            'produtos' => $lista_produtos,
            'formaspagamentos' => $lista_das_formaspagamento,
            'bancos' => $lista_banco,
            'contas' => $lista_bancos_contas,
            'tipocomprovativos' => $TipoComprovativo,
            'estados' => $estados,
            'hasMorePages' => $comprovativos_list->count() > $request->input('page', 1) * $NumeroPaginator,
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
        $dias_passado = $this->diasDatas($request->calDataBorderoux, $dataFinal);
        $NumeroDiaNecessario = 90;//$request->DiasMaximoRegistroComprovativo;

        $loan_number_v = "";

        $verificar_ = ComprovativoModel::verificarSeBorderouxExiste($request->txtVoucher, $request->calDataBorderoux, $request->selectBanco, 0);

        if ($verificar_) {

            $Mensagem = " Ups!, Já existe um comprovativo com o voucher indicado: [   Loan Number: " . $verificar_->BuDadoOrigem . " | ID Borderoux: " . $verificar_->BuReferencia . " | Montante: " . $verificar_->BuMontante . " | Data do Borderoux: " . $verificar_->BuData . " ] => Por favor contactar a DCF para  esclarecer esta situação.";

            return redirect()
                ->back()
                ->with('error', $Mensagem);
        } else {
            if ($dias_passado > $NumeroDiaNecessario) {
                return redirect()
                    ->back()
                    ->with('error', 'Ups!,  Comprovativo Muito antigo, excedeu o número de dias permitido  para ser cadastrado!, infelizmente não foi cadastrado.');
            } else {

                $request->validate([

                    'anexo' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
                ]);


                $comprovativonome = "";
                if ($request->hasFile('anexo')) {
                    $pathArquivo = $request->file('anexo')->store('comprovativos', 'public');

                    // Corrigido: Obtém apenas o nome do arquivo salvo
                    $comprovativonome = basename($pathArquivo);
                    // Salve $path no banco de dados
                }

                if ($cadastrar_o_que == "Loan") {

                    $siglabase = $request->selectBase;
                    $numeroLoanSanving = $request->txtNumeroLoanSaving;
                    $produto = $request->selectProdutoLoan;
                    $infoadicional = $request->txtInfoAdicional;
                    $formapgt = $request->selectFormaPagamento;
                    $telefone = $request->telefone;

                    $tipotransacao = "L04";
                    $banco = $request->selectBanco;
                    //Código estava armazenar valores de forma errada, ex.: 19.666,67 => 1.966.667
                    //$money = str_replace('.', '', $request->txtMontante);
                    //$money = str_replace(",", ".", $money);
                    $money = $request->txtMontante;
                    $dataBorderoux = $request->calDataBorderoux;
                    $voucher = $request->txtVoucher;
                    $contaBancaria = $request->selectBancoConta;

                    $loan_number = $siglabase;
                    $loan_number .= "/";
                    $loan_number .= $numeroLoanSanving;

                    // $_verifica_esta_aplicado_no_extrato = TKxextrato::verificarSeExtratoExiste($loan_number, $siglabase);

                    //  if (true) {


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
                        'BuData' => $dataBorderoux,
                        'BuContaBancaria' => $contaBancaria,
                        'Eliminado' => 0,
                        'idestado' => 1,
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
                    $banco = $request->selectBanco;
                    $money = str_replace('.', '', $request->txtMontante);
                    $money = str_replace(",", ".", $money);
                    $dataBorderoux = $request->calDataBorderoux;
                    $voucher = null;
                    $contaBancaria = $request->selectBancoConta;
                    $infoadicional = $request->txtInfoAdicional;
                    $telefone = $request->telefone;
                    $loan_number = $siglabase;
                    $loan_number .= "/";
                    $loan_number .= $siglaIndividualgrupal;
                    $loan_number .= $numeroLoanSanving;

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
                        'BuData' => $dataBorderoux,
                        'BuContaBancaria' => $contaBancaria,
                        'Eliminado' => 0,
                        'idestado' => 1,
                        'BaseOperacao' => $siglabase,
                        'infoadicional' => $infoadicional,
                        'telefonecliente' => $telefone,
                        'filecomprovativo' => $comprovativonome,

                    ]);
                }

                if ($insert) {
                    return Redirect::route('comprovativos')
                        ->with('success', 'Dados guardados com sucesso!');
                } else {
                    return Redirect::back()
                        ->with('error', 'Ups!, algo correu errado ao cadastrar comprovativo!');
                }
            }
        }
    }

    public function finalizaraeliminacao(Request $request, $id)
    {
        $hoje = date('d/m/Y');
        $Mensagem = "";

        $verica_existe_recupercao = RecuperacaoModel::where('id_comprovativo', $id)->first();

        if ($verica_existe_recupercao) {
            $Mensagem = " Ups!, O comprovativo não pode ser eliminado porque esta associado a uma recuperação! [   Loan Number: " . $verica_existe_recupercao->ReBuDadoOrigem . " | Voucher: " . $verica_existe_recupercao->ReBuReferencia . " | Montante: " . $verica_existe_recupercao->ReBuMontante . " | Cod. Recuperador: " . $verica_existe_recupercao->id_recuperador . " ] => Por favor contactar a DSO para  esclarecer esta situação.";
            return back()->with('error', $Mensagem);
        } else {
            $eliminar_comprovativo = ComprovativoModel::setEliminarComprovativo($id, session('LoggedUser'), $hoje, $request->txtMotivo, $request->txtDadosEliminado, $request->txtLoan);

            if ($eliminar_comprovativo) {
                return back()->with('success', 'Comprovativo eliminado com  sucesso!');
            } else {
                return back()->with('error', 'Ups! algo aconteceu errado  ao eliminar este comprovativo, por favor cotactar a DSO');
            }
        }
    }
    public static function diasDatas($data_inicial = '2013-08-01', $data_final = '2013-08-16')
    {
        $diferenca = strtotime($data_final) - strtotime($data_inicial);
        $dias = floor($diferenca / (60 * 60 * 24));
        return $dias;
    }
}
