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

class TesourariaController extends Controller
{
    //
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function viewRecuperacoesTesouria(Request $request)
    {


        $authenticatedUser = Auth::user();

        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $authenticatedUser->UtAgencia)->first();
        $NumeroRegistroTabela = $resultagencia_user->NumeroRegistroTabela;


        $ids_estados = $authenticatedUser->rec_viewestados;

        $Bases = $resultagencia_user->BasesOperacao;
        $ESTADO = $ids_estados;


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

        $lista_recuperacoes = RecuperacaoModel::getTesourariaRecuperacoes($NumeroRegistroTabela, $tipo, $estado, $agencia, $dataIn, $dataF, $loan);


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
                // 'datareconciliacao' => $item->datareconciliacao,
                // 'montante' => $item->ReBuMontante,
                // Mantenha todos os campos necessários para filtros client-side
                'CiFecha' => $item->CiFecha, // Para filtro por data
                'estado_id' => $item->id_estado, // Para filtro por estado
                // 'OfIdentificador' => $item->OfIdentificador, // Para filtro por agência
                'ReBuMontante' => $item->ReBuMontante // Para cálculos
            ];
        });


        return Inertia::render('Tesouraria', [
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
    public function confirmarMultiplas(Request $request)
    {



        $ids = $request->input('ids');
         $estado_id = $request->input('id_estado');
        $motivo_obs = $request->input('obs');


        $MES_ANO_PAGAMENTO = $request->input('mes_para_pagamento');

        $authenticatedUser = Auth::user();

        try {
            DB::beginTransaction();

            // Update records
            $updated = RecuperacaoModel::whereIn('id', $ids)
                ->update([
                    'data_do_pagamento' => $MES_ANO_PAGAMENTO,
                    'id_estado' => $estado_id
                ]);

            if ($updated) {

                // Create records for each ID
                foreach ($ids as $id) {

                    RecUtilizEstadModel::create([
                        'UtCodigo' => $authenticatedUser->UtCodigo,
                        'id_recuperacao' => $id,
                        'id_estado' => $estado_id
                    ]);
                }

                DB::commit();
                return back()->with('success', 'Recuperações realizada com sucesso!');
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
