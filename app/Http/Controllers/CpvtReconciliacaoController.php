<?php

namespace App\Http\Controllers;

use App\Models\CpvtReconciliacaoModel;
use App\Models\EstadosModel;
use Illuminate\Http\Request;
use App\Models\ComprovativoModel;
use App\Models\RecuperacaoModel;
use App\Models\TKxAgenciaModel;
use App\Models\TKxBancoContaModel;
use App\Models\TKxBancoModel;
use App\Models\TKxClProdutoModel;
use Illuminate\Support\Facades\Auth;

use Inertia\Inertia;

class CpvtReconciliacaoController extends Controller
{
    public function viewComprovativosReconlicacao(Request $request)
    {


        $authenticatedUser = Auth::user();

        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $authenticatedUser->UtAgencia)->first();


        $filtros = $request->only(['search', 'data_inicio', 'data_fim','estadoconsulta','agenciaconsulta']);


        $tipoDeBusca = $request->tipo;








        $NumeroRegistroTabela = $resultagencia_user->NumeroRegistroTabela;
        $dataFecho = $resultagencia_user->DataFecho;

        $dataFecho = date("Y-m-d", strtotime($dataFecho));
        $hoje = date('Ymd');
        $ConsultaBasesConsulta = "'" . $resultagencia_user->BasesOperacao . "'";
        $dataActual = date("Y-m-d", strtotime($hoje));
        $sistema_aberto = true;


        if ($dataFecho == $dataActual) {
            $sistema_aberto = false;
        } else {
            $sistema_aberto = true;
        }
         $estados = EstadosModel::getEstadosDCF('DCF');
         $ids_estados = $estados->pluck('id')->implode(',');

        $Bases = "'" . $resultagencia_user->BasesOperacao . "'";
        $ESTADO = "'" .$ids_estados. "'";

          $DataInicio = date("Y-m-d 00:00:00", strtotime('-7 day', strtotime($hoje)));
        $DataFim = date("Y-m-d 23:59:00", strtotime($hoje));
        $TIPO = 0;
        $LOAN = "'DS/280890'";

        $BasesOperacao = explode(',', $resultagencia_user->BasesOperacao);

        if ($tipoDeBusca == 1) {

            $DataInicio =date("Y-m-d 00:00:00", strtotime($request->data_inicio));
            $DataFim = date("Y-m-d 23:59:00", strtotime($request->data_fim));
            $TIPO = $tipoDeBusca;
            if($request->agenciaconsulta !='T'){
                $Bases= "'" .$request->agenciaconsulta . "'";
            }

            if($request->estadoconsulta !=28){
                $ESTADO="'" .$request->estadoconsulta. "'";
            }

           // dd( $DataFim);

        }

        if ($tipoDeBusca == 3) {
            $LOAN = "'" . $request->loan . "'";
            $TIPO = $tipoDeBusca;

        }

        $lista_comprovativo = ComprovativoModel::getComprovativos($Bases, $DataInicio, $DataFim, $NumeroRegistroTabela, $TIPO, $LOAN,$ESTADO);
        $lista_produtos = TKxClProdutoModel::getProdutos();
        $lista_banco = TKxBancoModel::getBancos();
        $lista_bancos_contas = TKxBancoContaModel::getBancosContas();
        $BasesOperacaoAgencias = TKxAgenciaModel::whereIn('OfIdentificador', $BasesOperacao)->get();

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
                'file' => $item->filecomprovativo,
                'usuario' => $item->UtNome,
                'lnr' => $item->BuDadoOrigem,
                'estado' => $item->estado,
                'color' => $item->color,
                'cliente' => $item->infoadicional,
                'observacao' => $item->observacao,

                'metodologia' => $item->PoAgrupado,
                'referencia' => $item->BuReferencia,
                'montante' => number_format($item->BuMontante, 2, ',', '.'),

            ];
        });
        $NumeroPaginator = 30;
        $paginado = $comprovativos_list->forPage($request->input('page', 1), $NumeroPaginator)->values();

        //dd($comprovativos_list);

        return Inertia::render('Reconciliacao', [
            'comprovativos' => $paginado,
            'filters' => $filtros,
            'page' => (int) $request->input('page', 1),
            'bases' => $BasesOperacaoAgencias,
            'produtos' => $lista_produtos,
            'bancos' => $lista_banco,
            'contas' => $lista_bancos_contas,
            'tipocomprovativos' => $TipoComprovativo,
            'estados' => $estados,
            'hasMorePages' => $comprovativos_list->count() > $request->input('page', 1) * $NumeroPaginator,
        ]);
    }

    public function validarComprovativo(Request $request)
    {
        $authenticatedUser = Auth::user();
        $hoje = date('Y-m-d H:i:s');

        // Verifica se já existe uma reconciliação para o comprovativo
        $reconciliacao = CpvtReconciliacaoModel::where('idcomprovativo', $request->id)->where('idestado', $request->estado)->first();

        if ($reconciliacao) {
            // Atualiza os campos se já existe
            /*    $reconciliacao->update([
                   'datareconciliacao' => $hoje,
                   'CodigoConta' => $request->conta,
                   'voucher' => $request->voucher,
                   'descricao' => $request->descricao,
                   'observacao' => $request->observacao,
                   'UtCodigo' => $authenticatedUser->UtCodigo,
                   'idestado' => $request->estado
               ]);
                return redirect()->back()->with('success', 'Comprovativo reconciliado (atualizado) com sucesso!');
               */


            return back()->with('error', 'Ups!, não foi possível reconciliar o comprovativo, duplicação de estado');
        } else {
            // Cria um novo registro
            $insert = CpvtReconciliacaoModel::create([
                'datareconciliacao' => $hoje,
                'CodigoConta' => $request->conta,
                'voucher' => $request->voucher,
                'descricao' => $request->descricao,
                'observacao' => $request->observacao,
                'idcomprovativo' => $request->id,
                'UtCodigo' => $authenticatedUser->UtCodigo,
                'idestado' => $request->estado
            ]);


            if ($insert) {
                $comprovativo = ComprovativoModel::where('id', $request->id)->first();

                //Ao reconciliar, o novo voucher da contabilidade deve reflectir no comprovativo, bem como o banco
                $comprovativo->update([
                    'idestado' => $request->estado,
                    'BuReferencia' => $request->voucher,
                    'BaCodigo' => $request->banco
                ]);
                return redirect()->back()->with('success', 'Comprovativo reconciliado (criado) com sucesso!');
            } else {
                return back()->with('error', 'Ups!, não foi possível reconciliar o comprovativo');
            }
        }
    }

     public function listarEstadosReconciliacao(){

          $estados = EstadosModel::getEstadosDCF('DCF');
        return response()->json($estados);
    }

}
