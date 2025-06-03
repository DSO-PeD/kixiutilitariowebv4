<?php

namespace App\Http\Controllers;

use App\Models\TKxAgenciaModel;
use App\Models\TKxBancoContaModel;
use App\Models\TKxBancoModel;
use App\Models\TKxClProdutoModel;
use App\Models\TKxCodigoCaeModel;
use App\Models\TKxExtratoModel;
use App\Services\IziPayService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class TKxExtratoController extends Controller
{
    public function viewExtrato(Request $request)
    {
        $authenticatedUser = Auth::user();
        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $authenticatedUser->UtAgencia)->first();

        $tipoDeBusca = $request->tipo;

        $hoje = date('Y-m-d');
        $DataInicio = date("Y-m-d 00:00:00", strtotime('-7 day', strtotime($hoje)));
        $DataFim = date("Y-m-d 23:59:00", strtotime($hoje));
        $TIPO = 0;
        $LOAN = "'DS/280891'";






        $NumeroRegistroTabela = $resultagencia_user->NumeroRegistroTabela;
        $dataFecho = $resultagencia_user->DataFecho;

        $dataFecho = date("Y-m-d", strtotime($dataFecho));


        $dataActual = date("Y-m-d", strtotime($hoje));
        $sistema_aberto = true;

        if ($dataFecho == $dataActual) {
            $sistema_aberto = false;
        } else {
            $sistema_aberto = true;
        }

        if ($tipoDeBusca == 1) {

            $DataInicio = date("Y-m-d 00:00:00", strtotime($request->data_inicio));
            $DataFim = date("Y-m-d 23:59:00", strtotime($request->data_fim));
            $TIPO = $tipoDeBusca;
        }

        if ($tipoDeBusca == 3) {
            $LOAN = $request->loan;
            $TIPO = $tipoDeBusca;

        }


        $Bases = "'" . $resultagencia_user->BasesOperacao . "'";

        $BasesOperacao = explode(',', $resultagencia_user->BasesOperacao);
        $ConsultaBaseConsulta = "'" . $resultagencia_user->BasesOperacao . "'";


        $page = request()->get('page', 1);
        $perPage = 30;

        $extratos = collect(TKxExtratoModel::getExtratosPorDataRegistro($Bases, $DataInicio, $DataFim, $NumeroRegistroTabela, $TIPO, $LOAN));
        $paginados = $extratos->forPage($page, $perPage)->values();
        $paginados->transform(fn($item) => (array) $item);

        $lista_produtos = TKxClProdutoModel::getProdutosDesembolsos();
        $lista_banco = TKxBancoModel::getBancos();
        $lista_bancos_contas = TKxBancoContaModel::getBancosContas();
        $lista_actividade_economica = TKxCodigoCaeModel::getActividadeEconomica();

        $BasesOperacaoAgencias = TKxAgenciaModel::whereIn('OfIdentificador', $BasesOperacao)->get();
        $lista_nes_grupo = TKxExtratoModel::getNecesidadesGrupo();
        $lista_nes_tipo = TKxExtratoModel::getNecesidadesTipo();

        //dd($extratos);

        return Inertia::render('Extratos', [
            'lista_extrato' => [
                'data' => $paginados,
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $extratos->count(),
            ],
            'BasesOperacao' => explode(',', $resultagencia_user->BasesOperacao),
            'agencia' => session('Agencia'),
            'produtosextratos' => $lista_produtos,
            'lista_banco' => $lista_banco,
            'lista_bancos_contas' => $lista_bancos_contas,
            'lista_actividade_economica' => $lista_actividade_economica,
            'sistema_aberto' => $sistema_aberto,
            'lista_nes_grupo' => $lista_nes_grupo,
            'lista_nes_tipo' => $lista_nes_tipo,
            'bases' => $BasesOperacaoAgencias,
        ]);



    }


    public function guardarDataExtrato(Request $request)
    {

        //  dd($request);
        // Validação dos dados do formulário
        /* $validator = Validator::make($request->all(), [
             // ... outras validações ...
             'txtValorTPAnte' => 'required_if:txtPecentTPAnte,!=,null|numeric',
             'txtValorIVATPAnte' => 'required_if:txtPecentTPAnte,!=,null|numeric',
         ], [
             'required_if' => 'O campo :attribute é obrigatório quando a percentagem antecipada está preenchida',
             'numeric' => 'O campo :attribute deve ser um número válido'
         ]);

         if ($validator->fails()) {
             return redirect()->back()
                 ->withErrors($validator)
                 ->withInput();
         }*/





        DB::beginTransaction();

        try {
            $authenticatedUser = Auth::user();
            $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $request->selectBase)->first();

            if (!$resultagencia_user) {
                throw new \Exception("Agência não encontrada");
            }

            // Formatar loan number
            $siglabase = $resultagencia_user->OfIdentificador;
            $loan_number = "{$siglabase}/{$request->txtNumeroLoan}";

            // Verificar se extrato já existe
            if (TKxExtratoModel::verificarSeExtratoExiste($loan_number, $siglabase)) {
                return redirect()->back()
                    ->with('error', 'Já existe um cálculo com este Loan Number!');
            }

            // Tratar necessidades especiais
            $necessidade = $this->tratarNecessidadesEspeciais($request);


            $tpaValorAnte = $this->formatDecimal($request->txtValorTPAnte) ?? 0;
            $tpaIvaAnte = $this->formatDecimal($request->txtValorIVATPAnte) ?? 0;

            $valor_borderoux_tp = 0;
            $valor_borderoux_tp = 0;
            $banco_tp = "";
            $conta_tp = "";
            $dataBorderoux_tp = "";
            $voucher_tp = "";

            $valor_borderoux_ti = 0;
            $valor_borderoux_ti = 0;
            $banco_ti = "";
            $conta_ti = "";
            $dataBorderoux_ti = "";
            $voucher_ti = "";


            //Dados das Taxas


            $tipo_tp_Ante = "Antecipado";
            $percent_tp_Ante = $request->txtPecentTPAnte;

            $valor_tpAnte = $request->txtValorTPAnte;



            if ($tipo_tp_Ante == 'Antecipado') {

                if ($valor_tpAnte == 0 || $percent_tp_Ante == '' || $percent_tp_Ante == null) {
                    $valor_borderoux_tp = 0;
                    $banco_tp = '';
                    $conta_tp = '';
                    $dataBorderoux_tp = '';
                    $voucher_tp = '';
                } else {

                    $valor_borderoux_tp = $request->txtMontanteBorderoux_TP;
                    $banco_tp = $request->selectBancoBorderoux_TP;
                    $conta_tp = $request->selectContaBancariaBorderoux_TP;
                    $dataBorderoux_tp = $request->dataBorderoux_TP;
                    $voucher_tp = $request->txtVoucherBorderou_TP;
                }

            }




            $tipo_ti = $request->TIPO_TI;


            if ($tipo_ti == 'Antecipado') {


                $valor_borderoux_ti = $request->txtMontanteBorderoux_TI;
                $banco_ti = $request->selectBancoBorderoux_TI;
                $conta_ti = $request->selectContaBancariaBorderoux_TI;
                $dataBorderoux_ti = $request->dataBorderoux_TI;
                $voucher_ti = $request->txtVoucherBorderou_TI;
            }




            // Fim dados das taxas




            // Preparar dados para inserção
            $data = [
                'UtCodigo' => $authenticatedUser->UtCodigo,
                'CiFecha' => now(),
                'OficialCredito' => $request->txtOficialCredito,
                'Lnr' => $loan_number,
                'Cliente' => $request->txtNomeCliente,
                'Produto' => $request->selectProduto,
                'ValorCreditoNoContrato' => $this->formatDecimal($request->txtValorCreditoNoContrato),
                'PercColateral' => $request->PecentCPD ?? 0,
                'ValorDoColateral' => $this->formatDecimal($request->txtValorColateralDepositado),
                'PercColateralDeduzido' => $request->txtPecentCDD ?? 0,
                'ValorDoColateralDeduzido' => $this->formatDecimal($request->txtValorColateralDeduzido),
                'ValorDoCredito' => $this->formatDecimal($request->txtValorCreditoNoContrato),
                'ValorTotalCredito' => $this->formatDecimal($request->txtFinalValorAReceber),
                'TaxaProcessamento' => 'PosAntecipado',
                'TXAProcePercenta' => $request->txtPecentTP,
                'TaxaProcessamentoAnte' => 'Antecipado',

                'TXAProcePercentaAnte' => $request->txtPecentTPAnte ?? 0,
                'TXAProcePercentaValorAnte' => $tpaValorAnte,
                'ValorIVATaxaProcessamentoAnte' => $tpaIvaAnte,
                'TXAProcePercentaValor' => $this->formatDecimal($request->txtValorTP),

                'ValorIVATaxaProcessamento' => $this->formatDecimal($request->txtValorIVATP),
                'TXAProceAnteBuReferencia' => $voucher_tp,
                'TXAProceAnteBuBanco' => $banco_tp,
                'TXAProceAnteBuNumeroConta' => $conta_tp,
                'TXAProceAnteBuMontante' => $this->formatDecimal($valor_borderoux_tp),
                'TXAProceAnteBuData' => $dataBorderoux_tp,
                'TaxaImprevisto' => $request->TIPO_TI,
                'TXAImprePercenta' => $request->txtPecentTI,
                'TXAImprePercentaValor' => $this->formatDecimal($request->txtValorTI),
                'ValorIVATaxaImprevisto' => $this->formatDecimal($request->txtValorIVATI),
                'TXAImpreAnteBuReferencia' => $voucher_ti,
                'TXAImpreBuBanco' => $banco_ti,
                'TXAImpreBuNumeroConta' => $conta_ti,
                'TXAImpreBuMontante' => $this->formatDecimal($valor_borderoux_ti),
                'TXAImpreBuData' => $dataBorderoux_ti,
                'ValorAKZTaxaDeConfirmacao' => $this->formatDecimal($request->txtValorTC),
                'DataRetiradaTaxaDeConfirmacao' => $request->data_tc,
                'ValorIVATaxaConfirmacao' => $this->formatDecimal($request->txtValorIVATC),
                'DescricaoActividadeEconomica' => $request->txtDecricaoAE,
                'CodigoAtividade' => $request->cbAE,
                'Sector' => $request->cbSector,
                'Magnitude' => $request->cbMagnitude,
                'RendaMensal' => $request->cbRendaMensal,
                'ValorPrimeiraPrestacao' => 0,
                'ppe' => $request->cbPPE,
                'NecessidadesEspeciais' => $necessidade,
                'Eliminado' => 0,
                'BaseOperacao' => $siglabase,
                'referenciapagamento' => $request->txtRefPagamento,
                'RefPgtActivo' => 0
            ];

            // Garantir que nenhum campo obrigatório fique nulo
            $data = array_map(function ($value) {
                return $value === null ? 0 : $value;
            }, $data);

            // Inserir no banco de dados
            $insert = TKxExtratoModel::create($data);

            DB::commit();

            return Redirect::route('extratos')
                ->with('success', 'Dados guardados com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            return Redirect::back()
                ->with('error', 'Erro ao salvar: ' . $e->getMessage())
                ->withInput();
        }
    }
    protected function formatDecimal($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }

        // Remove todos os caracteres não numéricos exceto vírgula e ponto
        $cleaned = preg_replace('/[^\d,.-]/', '', $value);

        // Substitui vírgula por ponto se for usado como separador decimal
        if (strpos($cleaned, ',') !== false && strpos($cleaned, '.') !== false) {
            // Se tem ambos, assume que vírgula é separador decimal
            $cleaned = str_replace('.', '', $cleaned);
            $cleaned = str_replace(',', '.', $cleaned);
        } elseif (strpos($cleaned, ',') !== false) {
            // Se só tem vírgula, substitui por ponto
            $cleaned = str_replace(',', '.', $cleaned);
        }

        return is_numeric($cleaned) ? (float) $cleaned : 0;
    }

    /**
     * Trata as informações de necessidades especiais
     */
    protected function tratarNecessidadesEspeciais($request)
    {
        if ($request->CNE == "Nao") {
            return "Cliente não tem necessidade especial";
        }

        $nes = $request->nes;
        $nes_desc = $request->txtDesc;

        $_nes_tipo = TKxExtratoModel::getNecesidadesTipoById($nes);

        if ($nes == 'OU04') {
            return $_nes_tipo[0]->codigotipones . "-" . $_nes_tipo[0]->descricaotipones . "(" . $nes_desc . ")";
        }

        return $_nes_tipo[0]->codigotipones . "-" . $_nes_tipo[0]->descricaotipones;
    }

    /**
     * Trata as informações de borderoux para taxas
     */




    public function criarReferencia(Request $request)
    {


        $data = $request->all(); // dados do formulário
        $client = new IziPayService();

        $response = $client->mainKxU($data);


        $dadosAtualizar = [
            'RefPgtActivo' => 1, // Substitua pelo nome correto do campo e valor
            // outros campos que deseja atualizar...
        ];



        if ($response == 201) {

            // Sucesso



            $resultado = TKxExtratoModel::where('referenciapagamento', $request->numero)->update($dadosAtualizar);
            // return back()->with('success', 'Activada  com sucesso!');
            return redirect()->back()->with('success', 'Referência activada com sucesso!');


        } else if ($response == 422) {

            return back()->with('error', 'Referência ' . $request->numero . 'já existe.');

        } else if ($response == 201) {
            return back()->with('error', ' Serviços de Activação de referencia de Indisponível');
        }





    }


 public static function index()
  {
    $todos_extratos = TKxExtratoModel::all();
    return $todos_extratos;
  }

  //Listas Todos Extratos por parametro
  public static function show($DataInicio)
  {

    $hoje = date('Y-m-d');
    $Bases = "'DJA'";
    $DataFim = $hoje;
    $TIPO = 3;
    $LOAN = "'DS/280890'";

    $listagem_extrat = TKxExtratoModel::getExtratosPorDataRegistro($Bases, $DataInicio, $DataFim, 0, $TIPO, $LOAN);
    return $listagem_extrat;
  }




}
