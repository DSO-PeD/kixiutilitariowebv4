<?php

namespace App\Http\Controllers;

use App\Models\ComissoesMaturidadeModel;
use App\Models\RecuperacaoModel;
use App\Models\RecuperadorModel;
use App\Models\TKxAgenciaModel;
use App\Models\EstadosModel;
use App\Models\RecUtilizEstadModel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class RecuperacaoDCFController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }


    //Funcão: altera para exportado e cria script KR_DATA_HOJE.sql Owner: Wawingi
    public function viewRecuperacoesDCF(Request $request)
    {


        $authenticatedUser = Auth::user();

        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $authenticatedUser->UtAgencia)->first();
        $NumeroRegistroTabela = $resultagencia_user->NumeroRegistroTabela;

        // $estados = EstadosModel::getEstadosRecupecao($resultagencia_user->rec_viewestados);
        $ids_estados = $authenticatedUser->rec_viewestados;

        $Bases = $resultagencia_user->BasesOperacao;
        $ESTADO = $ids_estados;
        $RECUPERADOR = '0';


        $tipo = $request->tipo;
        $estado = $request->estado_input;
        $agencia = $request->agencia_imput;
        $dataIn = null;
        $dataF = null;
        $loan = $request->loan;

        if ($tipo == 4) {
            $dataIn = date("Y-m-d 00:00:00", strtotime($request->data_inicio_imput));
            $dataF = date("Y-m-d 23:59:00", strtotime($request->data_fim_imput));

            if ($estado == '28') {
                $estado = $ESTADO;

                // dd($request->estado_input);
            }
            if ($agencia == 'T') {
                $agencia = $Bases;
            }




        }

        $lista_recuperacoes = RecuperacaoModel::getRecuperacoes($NumeroRegistroTabela, $tipo, $estado, $agencia, $dataIn, $dataF, $loan, $RECUPERADOR);


        //Estatistica em função do tipo de filtro da recuperação
        $estatistica = RecuperacaoModel::getRecuperacoesEstatistica($tipo, $estado, $agencia, $dataIn, $dataF);

        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $this->user->UtAgencia)->first();
        $listacomissoes_taxas = RecuperacaoModel::listarComissoesETaxas();


        $BasesOperacao = explode(',', $resultagencia_user->BasesOperacao);
        $ConsultaBaseConsulta = "'" . $resultagencia_user->BasesOperacao . "'";
        $dataFecho = date("Y-m-d", strtotime($resultagencia_user->DataFecho));
        $hoje = date('Y-m-d');
        $sistema_aberto = ($dataFecho != $hoje);

        $NumeroRegistroTabela = $resultagencia_user->NumeroRegistroTabela;
        $sigla_agencia_base = $resultagencia_user->OfIdentificador;
        $rec_viewestado = explode(',', $authenticatedUser->rec_viewestados);

        $listar_recuperador = RecuperadorModel::getRecuperadores($sigla_agencia_base);
        $listar_voucher_para_recuperacao = RecuperacaoModel::listarVoucherParaRecuperacao($ConsultaBaseConsulta, $this->user->rec_registra);

        $listar_estados = RecuperacaoModel::listarEstadoDeConsultaUsuario($rec_viewestado);
        $lista_agencias_consultas = RecuperacaoModel::listarBasesDeConsultaRecuperacao();
        $BasesOperacaoAgencias = TKxAgenciaModel::whereIn('OfIdentificador', $BasesOperacao)->get();

        $total = $lista_recuperacoes->count();
        $totalMontante = $lista_recuperacoes->sum('ReBuMontante');





        // dd($lista_recuperacoes);
        $recuperacoes_list = $lista_recuperacoes->map(function ($item) {

            $meses = [
                '01' => 'Janeiro',
                '02' => 'Fevereiro',
                '03' => 'Março',
                '04' => 'Abril',
                '05' => 'Maio',
                '06' => 'Junho',
                '07' => 'Julho',
                '08' => 'Agosto',
                '09' => 'Setembro',
                '10' => 'Outubro',
                '11' => 'Novembro',
                '12' => 'Dezembro',
            ];

            if (!empty($item->mes_ano_pagamento)) {
                $MES = explode('-', $item->mes_ano_pagamento);

                // Garante que o valor do mês tenha dois dígitos (ex: 08)
                $mes = str_pad($MES[1], 2, '0', STR_PAD_LEFT);

                // Protege caso o mês seja inválido
                $mesAnoFormatado = isset($meses[$mes])
                    ? $meses[$mes] . '/' . $MES[0]
                    : 'Mês inválido';
            } else {
                $mesAnoFormatado = 'Não definido';
            }

            return [
                'id' => $item->id,
                //'data' => $item->CiFecha,
                'UtCodigo' => $item->UtCodigo,
                //'basedelacamento' => $item->basedelacamento,
                'ReBuDataLPF' => $item->ReBuDataLPF,
                'UtNome' => $item->UtNome,
                'ReBuDadoOrigem' => $item->ReBuDadoOrigem,
                'estado' => $item->estado,
                'color' => $item->color,
                'infoadicional' => $item->infoadicional,
                'ReBuData' => $item->ReBuData,
                'nome_recuperador' => $item->nome_recuperador,
                'id_comprovativo' => $item->id_comprovativo,
                'id_estado' => $item->id_estado,
                'referencia' => $item->ReBuReferencia,
                'voucher' => $item->voucher,
                'mes_ano_pagamento' => $mesAnoFormatado,
                'valor_a_receber' => $item->valor_a_receber,
                'desconto_IRT' => $item->desconto_IRT,
                'comissao_bruta' => $item->comissao_bruta,
                'prazo_maturidade' => $item->prazo_maturidade,
                'taxa_comissao_percent' => $item->taxa_comissao_percent,
                'BaseOperacao' => $item->BaseOperacao,
                'dias_epe' => $item->dias_epe,
                'OfNombre' => $item->OfNombre,
                'obs'=>$item->obs,
                'Imagen'=>$item->Imagen,
                // 'datareconciliacao' => $item->datareconciliacao,
                // 'montante' => $item->ReBuMontante,
                // Mantenha todos os campos necessários para filtros client-side
                'CiFecha' => $item->CiFecha, // Para filtro por data
                'estado_id' => $item->id_estado, // Para filtro por estado
                // 'OfIdentificador' => $item->OfIdentificador, // Para filtro por agência
                'ReBuMontante' => $item->ReBuMontante // Para cálculos
            ];
        });


        return Inertia::render('RecuperacoesDCF', [
            'lista_recuperacoes' => $recuperacoes_list,
            'filters' => [
                'search' => $request->input('search_input', ''),
                'lnr' => $request->input('lnr_imput', ''),
                'estado' => $request->input('estado_input', 28), // Valor padrão 28 (Todos)
                'agencia' => $request->input('agencia_imput', 'T'), // Valor padrão 'T' (Todas)
                'data_inicio' => $request->input('data_inicio_imput', ''),
                'data_fim' => $request->input('data_fim_imput', '')
            ],
            'listar_recuperador' => $listar_recuperador,
            'listar_estados' => $listar_estados,
            'BasesOperacao' => $BasesOperacao,
            'lista_agencias_consultas' => $lista_agencias_consultas,
            'listar_voucher_para_recuperacao' => $listar_voucher_para_recuperacao,
            'estatistica' => $estatistica,
            'page' => (int) $request->input('page', 1),
            'bases' => $BasesOperacaoAgencias,
            'total' => $total,
            'montantetotal' => $totalMontante,
            'listacomissoes_taxas' => $listacomissoes_taxas
        ]);
    }

    public function guardar(Request $request)
    {
        $hoje = date('Y-m-d H:i:s');
        $dataFinal = date('Y-m-d');
        $authenticatedUser = Auth::user();

        $dias_passado = $this->diasDatas($request->txtDataLPF, $dataFinal);
        $estado_icial = 1;

        if ($dias_passado > 30) {

            $estado_icial = 4;
        }

        $loan_number = $request->Loan;

        $recuperador = $request->selectRecuperador;
        $dataLPF = $request->txtDataLPF;
        $idComprovativo = $request->textIdComprovativo;
        $money = $request->txtMontante;
        $dataBorderoux = $request->calDataBorderoux;
        $voucher = $request->txtVoucher;
        $contaBancaria = $request->selectBancoConta;
        $baseOpercao = $request->textBaseOperacao;
        $baCodigo = $request->txtBaCodigo;

        $ComissaoMaturidade = ComissoesMaturidadeModel::where('id', '=', $request->selectMaturidadeCredito)->first();

        $comissao_bruta = $money * ($ComissaoMaturidade->taxa_comissao_percent / 100);
        $desconto_IRT = $comissao_bruta * (6.5 / 100);
        $valor_a_receber = $comissao_bruta - $desconto_IRT;


        $result_recuperacao = RecuperacaoModel::verificarSeBorderouxExisteNaRecuperacao($idComprovativo);



        if ($result_recuperacao) {

            $Mensagem = " Ups!, Já existe uma recuperação com o voucher indicado: [   Loan Number: " . $result_recuperacao->ReBuDadoOrigem . " | Voucher: " . $result_recuperacao->ReBuReferencia . " | Montante: " . $result_recuperacao->ReBuMontante . " | Data do Borderoux: " . $result_recuperacao->ReBuData . " ] => Por favor contactar a DSO para  esclarecer esta situação.";

            return redirect()
                ->back()

                ->with('error', $Mensagem);
        } else {


            $insert = RecuperacaoModel::create([
                'CiFecha' => $hoje,
                'id_recuperador' => $recuperador,
                'id_comprovativo' => $idComprovativo,
                'id_estado' => $estado_icial,
                'ReBuDadoOrigem' => $loan_number,
                'ReBuReferencia' => $voucher,
                'ReBuMontante' => $money,
                'ReBuData' => $dataBorderoux,
                'ReBaCodigo' => $baCodigo,
                'ReBuDataLPF' => $dataLPF,
                'BuContaBancaria' => $contaBancaria,
                'Eliminado' => 0,
                'BaseOperacao' => $baseOpercao,
                'UtCodigo' => $authenticatedUser->UtCodigo,
                'id_comissoestaxas' => $request->selectMaturidadeCredito,
                'comissao_bruta' => $comissao_bruta,
                'desconto_IRT' => $desconto_IRT,
                'valor_a_receber' => $valor_a_receber

            ]);
        }

        if ($insert) {
            return Redirect::route('recuperacoes')
                ->with('success', 'Dados guardados com sucesso!');
        } else {
            return Redirect::back()
                ->with('error', 'Ups!, algo correu errado ao cadastrar comprovativo!');
        }
    }

    public function alterarEstadoOBS(Request $request, $id)
    {

        $hoje = date('d/m/Y');
        $obs_recuperacao = RecuperacaoModel::alterarEstadoOBS($id, $request->txtAdicionar);

        if ($obs_recuperacao) {
            return back()->with('success', 'Observação adicionado com sucesso!');
        } else {
            return back()->with('error', 'Ups! algo aconteceu errado  ao adicionar estado nesta recuperação, por favor contactar a DSO');
        }
    }
    public static function diasDatas($data_inicial, $data_final)
    {
        $diferenca = strtotime($data_final) - strtotime($data_inicial);
        $dias = floor($diferenca / (60 * 60 * 24));
        return $dias;
    }

    //Funcão: altera para exportado e cria script KR_DATA_HOJE.sql Owner: Wawingi
    public static function alteracaoEstadoExportar()
    {
        $ano_hoje = date("Ymd");
        $hora_hoje = date("His", strtotime('now'));

        $nome_arquivo = "KR-" . $ano_hoje . "-" . $hora_hoje . ".sql";

        if (file_exists(storage_path() . '/app/scripts/' . $nome_arquivo)) {
            unlink(storage_path() . '/app/scripts/' . $nome_arquivo);
        }

        $arquivo = fopen(storage_path() . '/app/scripts/' . $nome_arquivo, "w+");

        $data_array = RecuperacaoModel::getRecuperacoesParaMudarEstado();

        $count_range = count($data_array);
        $count_total_exportados = 0;
        $status = false;
        $INSERT_SQL = "\n";
        $MensagemDeErro = "Nenhum dado para exportar";

        for ($i = 0; $i < $count_range; $i++) {
            $localiza_recuperacao = $data_array[$i];

            if ($localiza_recuperacao) {
                $loan_number = "'" . $localiza_recuperacao->ReBuDadoOrigem . "'";
                $UtCodigoRecuperador = "'" . $localiza_recuperacao->UtCodigoRecuperador . "'";
                $data_lpf = date_create($localiza_recuperacao->ReBuDataLPF);
                $DatLPF = "'" . date_format($data_lpf, "Ymd H:i:s") . "'";
                $Montante = $localiza_recuperacao->ReBuMontante;

                $Agencia = TKxagenciaModel::where('OfIdentificador', '=', $localiza_recuperacao->BaseOperacao)->first();
                $Agencia = $Agencia->OfCodigo;
                $agencia_moeda = "'" . $Agencia . "-AOA'";

                $Localidade = "'" . $localiza_recuperacao->Localidade . "'";
                $data_registro = date_create($localiza_recuperacao->CiFecha);
                $DataRegistro = "'" . date_format($data_registro, "Ymd H:i:s") . "'";

                $INSERT_SQL .= "INSERT INTO tKxLoRecuperador (PeCodigo, UtCodigo, PagData, PagMontante, OfCodigo, Localidade, Ordem, Registo) VALUES ((select dbo.fduUsCodigo(4,$agencia_moeda,$loan_number)), $UtCodigoRecuperador, $DatLPF, $Montante, $Agencia, $Localidade, 1, $DataRegistro)";

                if ($localiza_recuperacao->id_estado == 1) {
                    $status = RecuperacaoModel::mudarEstadoParaExportado($localiza_recuperacao->id, $INSERT_SQL);
                    $MensagemDeErro = "Ups! ocorreu um erro ao exportar dados da recuperação: [ Nº de Registro:" . $localiza_recuperacao->id . " <> Loan Number: " . $loan_number . " ] contactar a DSO!";

                    fwrite($arquivo, $INSERT_SQL);
                    $INSERT_SQL = "\n";
                    $count_total_exportados++;
                }
            }
        }

        $NEW_LINE = "\n";
        fwrite($arquivo, $NEW_LINE);
        $PROCEDURE = "Exec pKxRecuperacaoGerar";
        fwrite($arquivo, $PROCEDURE);
        fclose($arquivo);

        if ($status) {
            //Baixar o ficheiro
            $path = storage_path() . '/app/scripts/' . $nome_arquivo;
            if (!file_exists($path)) {
                abort(404, 'File not found.');
            }
            return response()->download($path);
        } else {
            return response()->json($MensagemDeErro);
            //return back()->with('error', $MensagemDeErro);
        }
    }

    public function listarEstados()
    {
        $estados = EstadosModel::getEstados();
        return response()->json($estados);
    }

    public function listarAgencias()
    {
        $agencias = TKxAgenciaModel::getAgencias($this->user);
        return response()->json($agencias);
    }

    public function confirmarRecuperacao(Request $request)
    {
        $id = $tipo = $request->query('id');
        if ($id) {
            $recup = RecuperacaoModel::find($id);
            $recup->id_estado = 3;
            if ($recup->save()) {
                return Redirect::back()->with('success', 'Confirmado com sucesso!');
            }
            return Redirect::back()->with('error', 'Erro ao Confirmar');
        }
    }
    public function confirmarMultiplas(Request $request)
    {


        //$month = $request->input('month');
        //$year = $request->input('year');
        $ids = $request->input('ids');
        $estado_id = $request->input('id_estado');
        $motivo_obs = $request->input('obs');



        $authenticatedUser = Auth::user();

        try {
            DB::beginTransaction();

            // Update records
            $updated = RecuperacaoModel::whereIn('id', $ids)
                ->update([
                   'id_estado' => $estado_id,
                    'obs' => $motivo_obs
                ]);

            if ($updated) {

                // Create records for each ID
                foreach ($ids as $id) {

                    RecUtilizEstadModel::create([
                        'UtCodigo' => $authenticatedUser->UtCodigo,
                        'id_recuperacao' => $id,
                        'id_estado' => $estado_id,
                        'observacoes'=>$motivo_obs
                    ]);
                }

                DB::commit();
                return back()->with('success', 'Recuperações aprovado com sucesso!');
            } else {
                DB::rollBack();
                return back()->with('error', 'Nenhum registro foi atualizado.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro ao confirmar recuperações: ' . $e->getMessage());
        }


    }
}
