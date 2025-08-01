<?php

namespace App\Http\Controllers;

use App\Models\TKxAgenciaModel;
use App\Models\TKxBancoContaModel;
use App\Models\TKxBancoModel;
use App\Models\TKxClProdutoModel;
use App\Models\TKxCodigoCaeModel;
use App\Models\TKxExtratoModel;
use App\Services\IziPayService;
use Carbon\Carbon;
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

        $tipoDeBusca = $request->tipo;


        $Bases = "'" . $resultagencia_user->BasesOperacao . "'";

        $BasesOperacao = explode(',', $resultagencia_user->BasesOperacao);
        $ConsultaBaseConsulta = "'" . $resultagencia_user->BasesOperacao . "'";

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

            $TIPO = $tipoDeBusca;
        }



        // $page = request()->get('page', 1);
        // $perPage = 30;

        $extratos = collect(TKxExtratoModel::getExtratosPorDataRegistro($Bases, $DataInicio, $DataFim, $NumeroRegistroTabela, $TIPO, $LOAN));
        // $paginados = $extratos->forPage($page, $perPage)->values();
        //  $paginados->transform(fn($item) => (array) $item);

        collect($extratos)->max('CiFecha');
        collect($extratos)->min('CiFecha');

        $DataInicioFormatada = Carbon::parse($DataInicio)->format('d/m/Y');
        $DataFimFormatada = Carbon::parse($DataFim)->format('d/m/Y');


        $lista_produtos = TKxClProdutoModel::getProdutosDesembolsos();
        $lista_banco = TKxBancoModel::getBancos();
        $lista_bancos_contas = TKxBancoContaModel::getBancosContas();
        $lista_actividade_economica = TKxCodigoCaeModel::getActividadeEconomica();

        $BasesOperacaoAgencias = TKxAgenciaModel::whereIn('OfIdentificador', $BasesOperacao)->get();
        $lista_nes_grupo = TKxExtratoModel::getNecesidadesGrupo();
        $lista_nes_tipo = TKxExtratoModel::getNecesidadesTipo();

        $total = $extratos->count();
        $totalMontante = collect($extratos)->sum('ValorTotalCredito');

        $extrato_list = $extratos->map(function ($item) {
            return [
                'Num' => $item->Num,
                'CiFecha' => $item->CiFecha,
                'UtCodigo' => $item->UtCodigo,
                'OficialCredito' => $item->OficialCredito,
                'Lnr' => $item->Lnr,
                'Cliente' => $item->Cliente,
                'Produto' => $item->Produto,
                'ValorCreditoNoContrato' => $item->ValorCreditoNoContrato,
                'PercColateral' => $item->PercColateral,
                'ValorDoColateral' => $item->ValorDoColateral,
                'PercColateralDeduzido' => $item->PercColateralDeduzido,
                'ValorDoColateralDeduzido' => $item->ValorDoColateralDeduzido,
                'ValorDoCredito' => $item->ValorDoCredito,
                'ValorTotalCredito' => $item->ValorTotalCredito,
                'TaxaProcessamento' => $item->TaxaProcessamento,
                'TXAProcePercenta' => $item->TXAProcePercenta,
                'TXAProcePercentaValor' => $item->TXAProcePercentaValor,
                'ValorIVATaxaProcessamento' => $item->ValorIVATaxaProcessamento,
                'TaxaProcessamentoAnte' => $item->TaxaProcessamentoAnte,
                'TXAProcePercentaAnte' => $item->TXAProcePercentaAnte,
                'TXAProcePercentaValorAnte' => $item->TXAProcePercentaValorAnte,
                'ValorIVATaxaProcessamentoAnte' => $item->ValorIVATaxaProcessamentoAnte,
                'TXAProceAnteBuReferencia' => $item->TXAProceAnteBuReferencia,
                'TXAProceAnteBuBanco' => $item->TXAProceAnteBuBanco,

                'TXAProceAnteBuNumeroConta' => $item->TXAProceAnteBuNumeroConta,
                'TXAProceAnteBuMontante' => $item->TXAProceAnteBuMontante,
                'TaxaImprevisto' => $item->TaxaImprevisto,
                'TXAImprePercenta' => $item->TXAImprePercenta,
                'TXAImprePercentaValor' => $item->TXAImprePercentaValor,
                'ValorIVATaxaImprevisto' => $item->ValorIVATaxaImprevisto,

                'TXAImpreBuBanco' => $item->TXAImpreBuBanco,
                'TXAImpreBuNumeroConta' => $item->TXAImpreBuNumeroConta,
                'TXAImpreBuMontante' => $item->TXAImpreBuMontante,
                'TXAImpreBuData' => $item->TXAImpreBuData,
                'ValorAKZTaxaDeConfirmacao' => $item->ValorAKZTaxaDeConfirmacao,
                'DataRetiradaTaxaDeConfirmacao' => $item->DataRetiradaTaxaDeConfirmacao,

                'ValorIVATaxaConfirmacao' => $item->ValorIVATaxaConfirmacao,
                'DescricaoActividadeEconomica' => $item->DescricaoActividadeEconomica,
                'CodigoAtividade' => $item->CodigoAtividade,
                'Sector' => $item->Sector,
                'Magnitude' => $item->Magnitude,
                'RendaMensal' => $item->RendaMensal,


                'ValorPrimeiraPrestacao' => $item->ValorPrimeiraPrestacao,

                'ppe' => $item->ppe,
                'Eliminado' => $item->Eliminado,

                'BaseOperacao' => $item->BaseOperacao,
                'referenciapagamento' => $item->referenciapagamento,
                'RefPgtActivo' => $item->RefPgtActivo


            ];
        });


        return Inertia::render('Extratos', [

            'lista_extrato' => $extrato_list,
            'filters' => [
                'search' => $request->input('search_input', ''),
                'lnr' => $request->input('lnr_imput', ''),
                'estado' => $request->input('estado_input', 28), // Valor padrão 28 (Todos)
                'agencia' => $request->input('agencia_imput', 'T'), // Valor padrão 'T' (Todas)
                'data_inicio' => $request->input('data_inicio_imput', ''),
                'data_fim' => $request->input('data_fim_imput', '')
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
            'total' => $total,
            'montantetotal' => $totalMontante,
            'dataInicioPeriodo' =>  $DataFimFormatada,
            'dataFimPeriodo' => $DataInicioFormatada,
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

    public function carregaExtratosKP(Request $request)
    {

        $DataInicio = date("Y-m-d 00:00:00", strtotime($request->data_inicio));


        $extratos = TKxExtratoModel::whereDate('CiFecha', '>=', $DataInicio)->where('Eliminado', 0)->get();

        return response()->json($extratos);

    }


    public function finalizaraeliminacao(Request $request)
    {

        $hoje = date('d/m/Y');
        $Mensagem = "";
        $authenticatedUser = Auth::user();


        // Eliminação para utilizadores MASTERS cuidado
        if ($authenticatedUser->elimina_confirmado_exportado) {


            $ERASER = TKxExtratoModel::setEliminarExtratoMASTER($request->id);

            if ($ERASER) {
                return back()->with('success', 'Aplicação eliminado com  sucesso!');
            } else {
                return back()->with('error', 'Ups! algo aconteceu errado  ao eliminar esta aplicação, por favor cotactar o P&D');
            }



        } else {

            //Eliminação para utilizadores mini



                $updated = TKxExtratoModel::where('id', $request->id)
                    ->update([
                        'Eliminado' => 1,
                        'EliminadoPor' => $authenticatedUser->UtCodigo,

                    ]);

                if ($updated) {
                    return back()->with('success', 'Comprovativo eliminado com  sucesso!');
                } else {
                    return back()->with('error', 'Ups! algo aconteceu errado  ao eliminar este comprovativo, por favor cotactar a DSO');
                }



        }

    }





}
