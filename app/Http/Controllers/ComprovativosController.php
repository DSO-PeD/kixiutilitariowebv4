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
use App\Models\TKxExtratoModel;
use App\Models\PgtRefNotificacaoModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\IziPayService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Inertia\Inertia;
use Exception;

class ComprovativosController extends Controller
{

    public function viewComprovativos(Request $request)
    {
        $authenticatedUser = Auth::user();

        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $authenticatedUser->UtAgencia)->first();


        $tipoDeBusca = $request->tipo;
        $tipoProdutoPP = $request->filtrar_poupancas;
        $tipoProdutoPT = $request->filtrar_prestacoes;


        $lista_produtos = TKxClProdutoModel::getProdutos();
        $lista_das_formaspagamento = TKxClTipopagamentoModel::getFormasDePamentos();


        $NumeroRegistroTabela = $resultagencia_user->NumeroRegistroTabela;
        $dataFecho = $resultagencia_user->DataFecho;

        $dataFecho = date("Y-m-d", strtotime($dataFecho));
        $hoje = date('Y-m-d');

        $dataActual = date("Y-m-d", strtotime($hoje));


        $estados = EstadosModel::getEstadosDCF('DCF');
        $ids_estados = $estados->pluck('id')->implode(',');

        $produto_poupancas_busca = collect($lista_produtos)->where('TipoProduto', '=', 'S');
        $produto_poupancas_busca = "'" . $produto_poupancas_busca->pluck('Metodologia')->implode(',') . "'";


        $produto_prestacoes_busca = collect($lista_produtos)->where('TipoProduto', '=', 'L');
        $produto_prestacoes_busca = "'" . $produto_prestacoes_busca->pluck('Metodologia')->implode(',') . "'";

        $produtos_geral_busca = "'" . $lista_produtos->pluck('Metodologia')->implode(',') . "'";
        $formaspagamento_geral = "'" . $lista_das_formaspagamento->pluck('FormaPago')->implode(',') . "'";


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

        if ($tipoDeBusca == 500000) {
            $TIPO = $tipoDeBusca;
        }
        if ($tipoDeBusca == 7000000) {
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

        $totalMontanteRegistado = collect($lista_comprovativo)->where('TtCodigo', '=', 'L04')->where('idestado', 19)->sum('BuMontante');
        $totalMontantePoupancaRegistado = collect($lista_comprovativo)->where('TtCodigo', '=', 'S01')->where('idestado', 19)->sum('BuMontante');
        $totalMontanteReflete = collect($lista_comprovativo)->where('TtCodigo', '=', 'L04')->where('idestado', 8)->sum('BuMontante');
        $totalMontantePoupancaReflete = collect($lista_comprovativo)->where('TtCodigo', '=', 'S01')->where('idestado', 8)->sum('BuMontante');
        $totalMontanteInregulares = collect($lista_comprovativo)->where('TtCodigo', '=', 'L04')->whereIn('idestado', [9, 11, 13, 20])->sum('BuMontante');
        $totalMontantePoupancaInregulares = collect($lista_comprovativo)->where('TtCodigo', '=', 'S01')->whereIn('idestado', [9, 11, 13, 20])->sum('BuMontante');
        $totalMontantePGREF = collect($lista_comprovativo)->where('TtCodigo', '=', 'DJA')->sum('BuMontante');

        collect($lista_comprovativo)->max('CiFecha');
        collect($lista_comprovativo)->min('CiFecha');

        $DataInicioFormatada = Carbon::parse($DataInicio)->format('d/m/Y');
        $DataFimFormatada = Carbon::parse($DataFim)->format('d/m/Y');

        $lista_pendentes = TKuPendentesModel::whereIn('BaseOperacao', $BasesOperacao)->where('Tipo', 'R')->get();

        $TipoComprovativo = [
            'G' => 'G/',
            'I' => 'I/'
        ];

        /*** Associar com dados não relacionados */
        $lnrs = collect($lista_comprovativo)->pluck('BuDadoOrigem')->unique();
        $contactos = TKxExtratoModel::whereIn('Lnr', $lnrs)->pluck('Telefone','Lnr');

        $refs = collect($lista_comprovativo)->pluck('refPagamento')->unique();
        $ibans = PgtRefNotificacaoModel::whereIn('refPagamento', $refs)->pluck('nib', 'refPagamento');

        $comprovativos_list = collect($lista_comprovativo)->map(function ($item) use ($contactos,$ibans) { 
            $vcr_view = "-";

            if ($item->voucher == "" || $item->voucher == null) {
                $vcr_view = $item->BuReferencia;
            } else {
                $vcr_view = $item->voucher;
            }

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
                'referenciatransacao' => $item->BuReferenciaTransacao,
                'voucher' => $vcr_view,
                'FormaPagoN' => $item->FormaPagoN,
                'descricao' => $item->descricao,
                'operadordcf' => $item->operadordcf,
                'datareconciliacao' => $item->datareconciliacao,
                'montante' => $item->BuMontante,
                'TipoProduto' => $item->TipoProduto,
                // Mantenha todos os campos necessários para filtros client-side
                'CiFecha' => $item->CiFecha, // Para filtro por data
                'estado_id' => $item->idestado, // Para filtro por estado
                'OfIdentificador' => $item->OfIdentificador, // Para filtro por agência
                'BuMontante' => $item->BuMontante, // Para cálculos
                'refPagamento' => $item->refPagamento,
                'periodo_trans_pgr' => $item->periodo_trans_pgr,
                'telefone' => $contactos[$item->BuDadoOrigem] ?? 'N/A',
                'iban'     => $ibans[$item->refPagamento] ?? 'N/A'
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
            'total' => $total,
            'montantetotal' => $totalMontante,
            'totalMontantePoupanca' => $totalMontantePoupanca,
            'totalMontanteRegistado' => $totalMontanteRegistado,
            'totalMontantePoupancaRegistado' => $totalMontantePoupancaRegistado,
            'totalMontanteReflete' => $totalMontanteReflete,
            'totalMontantePoupancaReflete' => $totalMontantePoupancaReflete,
            'totalMontanteInregulares' => $totalMontanteInregulares,
            'totalMontantePoupancaInregulares' => $totalMontantePoupancaInregulares,
            'totalMontantePGREF' => $totalMontantePGREF,
            'formaspagamentos' => $lista_das_formaspagamento,
            'lista_pendentes' => $lista_pendentes,
            'totalPendente' => $lista_pendentes->count(),
            'dataInicioPeriodo' => $DataFimFormatada,
            'dataFimPeriodo' => $DataInicioFormatada,
        ]);
    }

    public function getClientData(Request $request)
    {
        try {
            // Decodificar o número completo
            // $completeNumber = urldecode($completeNumber);
            $completeNumber = $request->query('completeNumber');
            Log::info("Buscando dados do cliente", [
                'numero_completo' => $completeNumber,
                'ip' => request()->ip()
            ]);

            // Buscar primeiro na tabela comprovativos
            $clientData = DB::table('comprovativos')
                ->where('BuDadoOrigem', $completeNumber)
                ->select('infoadicional as nome', 'telefonecliente as telefone')
                ->first();

            if ($clientData) {
                Log::info("Dados encontrados na tabela comprovativos", [
                    'numero_completo' => $completeNumber,
                    'cliente' => $clientData->nome
                ]);

                return response()->json([
                    'nome' => $clientData->nome,
                    'telefone' => $clientData->telefone,
                    'success' => true
                ]);
            }

            // Se não encontrou, buscar na tabela tkxextrato
            $clientData = DB::table('tkxextrato')
                ->where('Lnr', $completeNumber)
                ->select('Cliente as nome', 'Telefone as telefone')
                ->first();

            if ($clientData) {
                Log::info("Dados encontrados na tabela tkxextrato", [
                    'numero_completo' => $completeNumber,
                    'cliente' => $clientData->nome
                ]);

                return response()->json([
                    'nome' => $clientData->nome,
                    'telefone' => $clientData->telefone,
                    'success' => true
                ]);
            }



            // Se não encontrou, buscar na tabela referenciasmanuais
            $clientData = DB::table('referenciasmanuais')
                ->where('BuDadoOrigem', $completeNumber)
                ->select('nomecliente as nome', 'telefone as telefone')
                ->first();

            if ($clientData) {
                Log::info("Dados encontrados na tabela tkxextrato", [
                    'numero_completo' => $completeNumber,
                    'cliente' => $clientData->nome
                ]);

                return response()->json([
                    'nome' => $clientData->nome,
                    'telefone' => $clientData->telefone,
                    'success' => true
                ]);
            }



            Log::warning("Cliente não encontrado em nenhuma tabela", [
                'numero_completo' => $completeNumber
            ]);

            return response()->json([
                'error' => 'Cliente não encontrado',
                'message' => $completeNumber,
                'success' => false
            ], 404);

        } catch (\Exception $e) {
            Log::error("Erro ao buscar dados do cliente", [
                'error' => $e->getMessage(),
                'numero_completo' => $completeNumber ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erro interno do servidor',
                'success' => false
            ], 500);


        }
    }

    public function guardar(Request $request)
    {
        try {
            $authenticatedUser = Auth::user();
            $cadastrarTipo = $request->ls;
            $montante = $request->txtMontante;


            // Validar montante máximo
            if ($montante > 7000000) {
                $montanteFormatado = number_format($montante, 2, ',', '.');
                $mensagem = "Ups! O montante excede 7.000.000,00: [MONTANTE: {$montanteFormatado}] => Comprovativo não cadastrado.";
                return redirect()->back()->with('error', $mensagem);
            }

            // Processar arquivo
            $nomeArquivo = null;
            if ($request->hasFile('anexo')) {
                $pathArquivo = $request->file('anexo')->store('comprovativos', 'public');
                $nomeArquivo = basename($pathArquivo);
            }

            // Format data
            $dataBorderoux = Carbon::createFromFormat('d/m/Y', $request->calDataBorderoux)->format('Y-m-d');

            // Preparar dados comuns
            $dadosComuns = [
                'CiFecha' => now(),
                'UtCodigo' => $authenticatedUser->UtCodigo,
                'BuMontante' => $montante,
                'BuData' => $dataBorderoux,
                'Eliminado' => 0,
                'filecomprovativo' => $nomeArquivo,
                'telefonecliente' => $request->telefone,
                'infoadicional' => $request->txtInfoAdicional
            ];

            // Processar por tipo (Loan ou Saving)
            if ($cadastrarTipo === "Loan") {
                $dados = $this->processarLoan($request, $dadosComuns);
            } else {
                $dados = $this->processarSaving($request, $dadosComuns);
            }

            // Inserir no banco de dados
            $comprovativo = ComprovativoModel::create($dados);

            if (!$comprovativo) {
                throw new Exception('Falha ao inserir comprovativo');
            }

            // Processar reconciliação se necessário
            /*  if ($request->selectFormaPagamento == 14) {
                  $this->processarReconciliacao($request, $comprovativo->id);
              }*/

            return redirect()->route('comprovativos')
                ->with('success', 'Dados guardados com sucesso!');

        } catch (Exception $e) {
            Log::error('Erro ao guardar comprovativo: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erro ao processar comprovativo: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function guardarmobile(Request $request)
    {

        try {

            $cadastrarTipo = $request->tipo_conta;
            $montante = $request->montante;

            // Validar montante máximo
            if ($montante > 7000000) {
                $montanteFormatado = number_format($montante, 2, ',', '.');
                $mensagem = "Ups! O montante excede 7.000.000,00: [MONTANTE: {$montanteFormatado}] => Comprovativo não cadastrado.";
                return response()->json([
                    'status' => 'ERROR',
                    'message' => "Ups! O montante excede 7.000.000,00: [MONTANTE: {$montanteFormatado}] => Comprovativo não cadastrado."
                ], 401);
            }

            // Processar arquivo
            $nomeArquivo = null;
            if ($request->hasFile('anexo_comprovativo')) {
                $pathArquivo = $request->file('anexo_comprovativo')->store('comprovativos', 'public');
                $nomeArquivo = basename($pathArquivo);
            }

            // Format data
            $dataBorderoux = Carbon::createFromFormat('d/m/Y', $request->data_reembolso)->format('Y-m-d');

            // Preparar dados comuns
            $dadosComuns = [
                'CiFecha' => now(),
                'UtCodigo' => $request->UtCodigo,
                'BuMontante' => $montante,
                'BuData' => $dataBorderoux,
                'Eliminado' => 0,
                'filecomprovativo' => $nomeArquivo,
                'telefonecliente' => $request->telefone,
                'infoadicional' => $request->nome_cliente,
                'pluscode_localderegistro' => $request->pluscode
            ];

            // Processar por tipo (Loan ou Saving)
            if ($cadastrarTipo === "Loan") {

                $dados = $this->processarLoan($request, $dadosComuns);

            } else {

                $dados = $this->processarSaving($request, $dadosComuns);

            }

            // Inserir no banco de dados
            $comprovativo = ComprovativoModel::create($dados);

            if (!$comprovativo) {
                throw new Exception('Falha ao inserir comprovativo');
            }



            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Dados guardados com sucesso!'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Dados não foram guardados!'
            ], 401);
        }
    }

    /**
     * Processar dados para Loan
     */
    private function processarLoan(Request $request, array $dadosBase)
    {

        $formaPagamento = $request->selectFormaPagamento;
        // $estado = $formaPagamento == 14 ? 8 : 1;
        $estado = 19;

        $loanNumber = $request->selectBase . '/' . $request->txtNumeroLoanSaving;

        $contaBancaria = $request->conta;
        $voucher = $request->txtVoucher;

        /*  if ($formaPagamento == 14) {
              $conta = TKxBancoContaModel::where('codigoConta', $request->conta)->first();
              if ($conta) {
                  $contaBancaria = $conta->ContaBacaria;
              }
          }*/

        return array_merge($dadosBase, [
            'BaCodigo' => $request->banco,
            'TtCodigo' => 'L04',
            'FormaPago' => $formaPagamento,
            'PoCodigo' => $request->selectProdutoLoan,
            'BuDadoOrigem' => $loanNumber,
            'BuReferencia' => $request->txtVoucher,
            'BuContaBancaria' => $contaBancaria,
            'idestado' => $estado,
            'BaseOperacao' => $request->selectBase
        ]);
    }

    /**
     * Processar dados para Saving
     */
    private function processarSaving(Request $request, array $dadosBase)
    {
        $formaPagamento = $request->selectFormaPagamento;
        // $estado = $formaPagamento == 14 ? 8 : 1;
        $estado = 19;

        $loanNumber = $request->selectBase . '/' .
            $request->selectGrupoIndividual . '/' .
            $request->txtNumeroLoanSaving;

        $contaBancaria = $request->conta;
        $voucher = null;

        /*if ($formaPagamento == 14) {
            $conta = TKxBancoContaModel::where('codigoConta', $request->conta)->first();
            if ($conta) {
                $contaBancaria = $conta->ContaBacaria;
                $voucher = $request->txtVoucher;
            }
        }*/

        return array_merge($dadosBase, [
            'BaCodigo' => $request->banco,
            'TtCodigo' => 'S01',
            'FormaPago' => $formaPagamento,
            'PoCodigo' => $request->selectProdutoSaving,
            'BuDadoOrigem' => $loanNumber,
            'BuReferencia' => $voucher,
            'BuContaBancaria' => $contaBancaria,
            'idestado' => $estado,
            'BaseOperacao' => $request->selectBase
        ]);
    }

    /**
     * Processar reconciliação
     */
    private function processarReconciliacao(Request $request, $idComprovativo)
    {
        return CpvtReconciliacaoModel::create([
            'datareconciliacao' => now(),
            'CodigoConta' => $request->conta,
            'voucher' => $request->txtVoucher,
            'descricao' => 'Inserção Automática',
            'observacao' => 'Comprovativo com Montante Depositado',
            'idcomprovativo' => $idComprovativo,
            'UtCodigo' => 'dcf',
            'idestado' => 8
        ]);
    }

    public function editarMontante(Request $request)
    {

        $request->validate([
            'id' => 'required|exists:comprovativos,id',
            'novo_montante' => 'required|numeric|min:0.01'
        ]);

        $comprovativo = ComprovativoModel::findOrFail($request->id);


        // Atualizar montante
        $comprovativo->update([
            'BuMontante' => $request->novo_montante
        ]);

        return back()->with('success', 'Montante atualizado com sucesso!');
    }

    public function editarDataRegistro(Request $request)
    {

        $request->validate([
            'id' => 'required|exists:comprovativos,id'

        ]);

        $comprovativo = ComprovativoModel::findOrFail($request->id);


        // Atualizar montante
        $comprovativo->update([
            'updated_at' => $request->nova_data
        ]);

        return back()->with('success', 'Data actualizada com sucesso');
    }

    public function editarVoucher(Request $request)
    {

        $request->validate([
            'id' => 'required|exists:comprovativos,id'

        ]);

        $comprovativo = ComprovativoModel::findOrFail($request->id);


        // Atualizar voucher
        $comprovativo->update([
            'BuReferencia' => $request->novoVoucher
        ]);

        $reconciliacao = CpvtReconciliacaoModel::where('idcomprovativo', '=', $request->id);

        $reconciliacao->update([
            'voucher' => $request->novoVoucher
        ]);

        return back()->with('success', 'Data actualizada com sucesso');
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

    // REQUISÕES MOBILE *********************************************************************************************
    public function viewComprovativosMobile(Request $request)
    {

        //  $authenticatedUser = TKxUsUtilizadorModel::where('UtCodigo', '=', 'albe.ebo')->first(); //Auth::user();
        // $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', 2)->first();


        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $request->UtAgencia)->first();



        $tipoDeBusca = $request->tipo;
        $tipoProdutoPP = $request->filtrar_poupancas;
        $tipoProdutoPT = $request->filtrar_prestacoes;



        $lista_produtos = TKxClProdutoModel::getProdutos();
        $lista_das_formaspagamento = TKxClTipopagamentoModel::getFormasDePamentos();



        $NumeroRegistroTabela = $resultagencia_user->NumeroRegistroTabela;
        $dataFecho = $resultagencia_user->DataFecho;

        $dataFecho = date("Y-m-d", strtotime($dataFecho));
        $hoje = date('Y-m-d');

        $dataActual = date("Y-m-d", strtotime($hoje));


        $estados = EstadosModel::getEstadosDCF('DCF');
        $ids_estados = $estados->pluck('id')->implode(',');

        $produto_poupancas_busca = collect($lista_produtos)->where('TipoProduto', '=', 'S');
        $produto_poupancas_busca = "'" . $produto_poupancas_busca->pluck('Metodologia')->implode(',') . "'";


        $produto_prestacoes_busca = collect($lista_produtos)->where('TipoProduto', '=', 'L');
        $produto_prestacoes_busca = "'" . $produto_prestacoes_busca->pluck('Metodologia')->implode(',') . "'";

        $produtos_geral_busca = "'" . $lista_produtos->pluck('Metodologia')->implode(',') . "'";
        $formaspagamento_geral = "'" . $lista_das_formaspagamento->pluck('FormaPago')->implode(',') . "'";


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

        if ($tipoDeBusca == 500000) {
            $TIPO = $tipoDeBusca;
        }
        if ($tipoDeBusca == 7000000) {
            $TIPO = $tipoDeBusca;
        }



        $lista_comprovativo = ComprovativoModel::getComprovativos($Bases, $DataInicio, $DataFim, $NumeroRegistroTabela, $TIPO, $LOAN, $ESTADO, $produtos_geral_busca, $formaspagamento_geral);


        //  $lista_banco = TKxBancoModel::getBancos();
        //  $lista_bancos_contas = TKxBancoContaModel::getBancosContas();

        $estados = EstadosModel::getEstadosDCF('DCF');
        /* $BasesOperacaoAgencias = TKxAgenciaModel::whereIn('OfIdentificador', $BasesOperacao)->get();
         $total = sizeof($lista_comprovativo);

         $totalMontante = collect($lista_comprovativo)->where('TtCodigo', '=', 'L04')->sum('BuMontante');
         $totalMontantePoupanca = collect($lista_comprovativo)->where('TtCodigo', '=', 'S01')->sum('BuMontante');

         $totalMontanteRegistado = collect($lista_comprovativo)->where('TtCodigo', '=', 'L04')->where('idestado', 19)->sum('BuMontante');
         $totalMontantePoupancaRegistado = collect($lista_comprovativo)->where('TtCodigo', '=', 'S01')->where('idestado', 19)->sum('BuMontante');
         $totalMontanteReflete = collect($lista_comprovativo)->where('TtCodigo', '=', 'L04')->where('idestado', 8)->sum('BuMontante');
         $totalMontantePoupancaReflete = collect($lista_comprovativo)->where('TtCodigo', '=', 'S01')->where('idestado', 8)->sum('BuMontante');
         $totalMontanteInregulares = collect($lista_comprovativo)->where('TtCodigo', '=', 'L04')->whereIn('idestado', [9, 11, 13, 20])->sum('BuMontante');
         $totalMontantePoupancaInregulares = collect($lista_comprovativo)->where('TtCodigo', '=', 'S01')->whereIn('idestado', [9, 11, 13, 20])->sum('BuMontante');
         $totalMontantePGREF = collect($lista_comprovativo)->where('TtCodigo', '=', 'DJA')->sum('BuMontante');*/

        collect($lista_comprovativo)->min('CiFecha');
        collect($lista_comprovativo)->max('CiFecha');


        $DataInicioFormatada = Carbon::parse($DataInicio)->format('d/m/Y');
        $DataFimFormatada = Carbon::parse($DataFim)->format('d/m/Y');



        $lista_pendentes = TKuPendentesModel::whereIn('BaseOperacao', $BasesOperacao)->where('Tipo', 'R')->get();
        
        $TipoComprovativo = [
            'G' => 'G/',
            'I' => 'I/'
        ];


        if ($lista_comprovativo) {



            // Definindo a estrutura da resposta JSON
            $response = [
                //'status' => 'SUCCESS',
                //'title' => 'Lista de Veiculos Disponíveis para Aluguel',
                'listacomprovativos' => $lista_comprovativo
            ];

            // Retornando a resposta JSON
            return response()->json($response, 200);
        } else {
            return response()->json(["title" => "Não existe comprovativo"]);
        }
    }

    // PAGAMENTOS POR REFERENCIA MANUAIS:******************************************************************

    public function viewReferenciaPGT(Request $request)
    { 


        $authenticatedUser = Auth::user(); 

        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $authenticatedUser->UtAgencia)->first();

        $tipoDeBusca = $request->tipo;
        $tipoProdutoPP = $request->filtrar_poupancas;
        $tipoProdutoPT = $request->filtrar_prestacoes;

        $lista_produtos = TKxClProdutoModel::getProdutos(); 

        $NumeroRegistroTabela = $resultagencia_user->NumeroRegistroTabela;
        $dataFecho = $resultagencia_user->DataFecho;

        $dataFecho = date("Y-m-d", strtotime($dataFecho));
        $hoje = date('Y-m-d');

        $dataActual = date("Y-m-d", strtotime($hoje));


        $estados = EstadosModel::getEstadosDCF('RPGT');
        $ids_estados = $estados->pluck('id')->implode(',');

        $produto_poupancas_busca = collect($lista_produtos)->where('TipoProduto', '=', 'S');
        $produto_poupancas_busca = "'" . $produto_poupancas_busca->pluck('Metodologia')->implode(',') . "'";


        $produto_prestacoes_busca = collect($lista_produtos)->where('TipoProduto', '=', 'L');
        $produto_prestacoes_busca = "'" . $produto_prestacoes_busca->pluck('Metodologia')->implode(',') . "'";

        $produtos_geral_busca = "'" . $lista_produtos->pluck('Metodologia')->implode(',') . "'";

        $Bases = "'" . $resultagencia_user->BasesOperacao . "'";

        $ESTADO = "'" . $ids_estados . "'";
        $DataInicio = date("Y-m-d 00:00:00", strtotime('-7 day', strtotime($hoje)));
        $DataFim = date("Y-m-d 23:59:00", strtotime($hoje));

        $TIPO = 7371;
        $LOAN = "'DS/280890'";

        $BasesOperacao = explode(',', $resultagencia_user->BasesOperacao); 

        if ($tipoDeBusca == 1) {
            $DataInicio = date("Y-m-d 00:00:00", strtotime($request->data_inicio));
            $DataFim = date("Y-m-d 23:59:00", strtotime($request->data_fim));
            $TIPO = $tipoDeBusca;
        }

        if ($tipoDeBusca == 3) {
            $LOAN = "'" . $request->loan . "'";
            $TIPO = 73713;
        }
        if ($tipoDeBusca == 4) {

            $DataInicio = date("Y-m-d 00:00:00", strtotime($request->data_inicio_imput));
            $DataFim = date("Y-m-d 23:59:00", strtotime($request->data_fim_imput));


            if ($request->agencia_imput !== 'T') {
                $Bases = "'" . $request->agencia_imput . "'";
            }

            $TIPO = 73714;
        }
    
        $lista_comprovativo = ComprovativoModel::getComprovativos($Bases, $DataInicio, $DataFim, $NumeroRegistroTabela, $TIPO, $LOAN, $ESTADO, $produtos_geral_busca, "'DJA'");

        $BasesOperacaoAgencias = TKxAgenciaModel::whereIn('OfIdentificador', $BasesOperacao)->get();
        $total = sizeof($lista_comprovativo);


        collect($lista_comprovativo)->max('created_at');
        collect($lista_comprovativo)->min('created_at');

        $DataInicioFormatada = Carbon::parse($DataInicio)->format('d/m/Y');
        $DataFimFormatada = Carbon::parse($DataFim)->format('d/m/Y');

        $TipoComprovativo = [
            'G' => 'G/',
            'I' => 'I/'
        ];
      
        $comprovativos_list = collect($lista_comprovativo)->map(function ($item) {
            return [
                'id' => $item->id,
                'data' => $item->dataRegistoFomatada,
                'agencia' => $item->OfNombre,
                'basedelacamento' => $item->basedelacamento,
                'usuario' => $item->UtNome,
                'lnr' => $item->BuDadoOrigem,
                'inicio' => $item->inicio,
                'fim' => $item->fim,
                'estado' => $item->estado,
                'color' => $item->color,
                'idestado' => $item->idestado,
                'cliente' => $item->nomecliente,
                'telefone' => $item->telefone,
                'metodologia' => $item->PoAgrupado,
                'referencia' => $item->referencia,
                'montante' => $item->montante,
                'montantepago' => $item->montantepago,
                'TipoProduto' => $item->TipoProduto,
                // Mantenha todos os campos necessários para filtros client-side
                'CiFecha' => $item->created_at, // Para filtro por data
                'OfIdentificador' => $item->OfIdentificador, // Para filtro por agência
            ];
        });

        $NumeroPaginator = 30;
        
        return Inertia::render('ReferenciasPGT', [
            'lista_comprovativo' => $comprovativos_list,
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

            'tipocomprovativos' => $TipoComprovativo,
            'estados' => $estados,
            'total' => $total,

            'dataInicioPeriodo' => $DataFimFormatada,
            'dataFimPeriodo' => $DataInicioFormatada
        ]);
    }

    public function guardarRegerenciaPagamento(Request $request)
    {
        $authenticatedUser = Auth::user();

        try {

            // Verificar se a referência já existe
            $referenciaExistente = DB::table('referenciasmanuais')
                ->where('referencia', $request->txtRefPagamento)
                ->first();

            if ($referenciaExistente) {

                return redirect()->back()
                    ->with('error', 'Esta referência de pagamento já está em uso' . $referenciaExistente)
                    ->withInput();
            }
            
            $siglaagencia = TKxAgenciaModel::where('OfCodigo', $request->selectBase)->first();
            $loanNumber = $siglaagencia->OfIdentificador . '/' . $request->selectGrupoIndividual . '/' . $request->txtNumeroLoanSaving;


            $dados_activar_referencia = [
                "numero" => $request->txtRefPagamento,
                "validade" => Carbon::now()->addDays(3)->format('d/m/Y H:i'),
                "montante" => number_format($request->txtMontante, 2, ',', ' '),
                "cliente" => [
                    "nome" => $request->txtInfoAdicional,
                    "email" => "diversos@kxicredito.ao",
                    "telefone" => $request->telefone,
                ],
                "metadados" => [
                    "item1" => "Activação de referência de pagamento no ambiente prod.",
                    "item2" => "Manual",
                ],
            ];

            $client = new IziPayService();
            $response = $client->mainKxU($dados_activar_referencia);

            if ($response == 201) {               // Sucesso

                // Preparar os dados para inserção
                $dadosReferencia = [
                    'BuDadoOrigem' => $loanNumber,
                    'nomecliente' => $request->txtInfoAdicional,
                    'telefone' => $request->telefone,
                    'PoCodigo' => $request->selectProdutoSaving,
                    'tipo' => $request->selectGrupoIndividual,
                    'referencia' => $request->txtRefPagamento,
                    'inicio' => Carbon::now(),
                    'fim' => Carbon::now()->addDays(3),
                    'montante' => $request->txtMontante,
                    'idestado' => 21,
                    'BaseOperacao' => $siglaagencia->OfIdentificador,
                    'activo' => 1, // Mudado para 1 para indicar que está ativo
                    'UtCodigo' => $authenticatedUser->UtCodigo,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

                // Inserir na base de dados
                $id = DB::table('referenciasmanuais')->insertGetId($dadosReferencia);

                // Buscar dados completos para retorno
                $referenciaCompleta = DB::table('referenciasmanuais')
                    ->where('id', $id)
                    ->first();

                if ($dadosReferencia) {
                    $validKey = config('djanotifpgtref.callback_access_key');

                    $telefone = null;
                    $mensagem = "Pagamento KIXICREDITO\n\n" .
                        "Referência {$request->txtRefPagamento}\n" .
                        "Valor Kz " . number_format($request->txtMontante, 2, ',', '.') . "\n" .
                        "Cliente {$loanNumber}\n\n" .
                        "Validade 72 horas\n\n" .
                        "KIXICREDITO\n" .
                        "PARCEIRA NOS NEGÓCIOS";

                    $telefone = $request->telefone;

                    if ($telefone) {
                        $response = Http::withHeaders([
                            'Access-Key' => $validKey,
                            'Content-Type' => 'application/json',
                        ])->post('http://kixisms.kixicredito.com/api/enviarSMS', [
                                    'contacto' => $telefone,
                                    'mensagem' => $mensagem,
                                ]);
                    }
                    Log::info('Tentativa de envio SMS', ['telefone' => $telefone, 'mensagem ' => $mensagem, 'montante' => $request->txtMontante]);
                }

                return redirect()->route('referenciapgt')
                    ->with('success', 'Referência de pagamento guardada com sucesso!');


            } else if ($response == 422) {

                return back()->with('error', 'Referência ' . $request->numero . 'já existe.');

            } else if ($response == 201) {
                return back()->with('error', 'Lamentamos, O Serviços de Activação de referencia  Indisponível');
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao processar referência de pagamento: ' . $e->getMessage())
                ->withInput();
        }
    }
}
