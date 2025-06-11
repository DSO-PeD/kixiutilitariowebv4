<?php

namespace App\Http\Controllers;

use App\Models\ComprovativoModel;
use App\Models\RecuperacaoModel;
use App\Models\TKxAgenciaModel;
use App\Models\TKxBancoContaModel;
use App\Models\TKxBancoModel;
use App\Models\TKxClProdutoModel;
use App\Models\TKxClTipopagamentoModel;
use App\Models\TKxCodigoCaeModel;
use App\Models\TKxExtratoModel;
use App\Models\TKxUsUtilizadorModel;
use App\Models\User;
use App\TkxclProdutos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        // Validação básica
        $request->validate([
            'UtCodigo' => ['required', 'string'],
            'UtSenha' => ['required', 'string'],
        ]);

        $credentials = $request->only('UtCodigo', 'UtSenha');

        $user = TKxUsUtilizadorModel::where('UtCodigo', $credentials['UtCodigo'])
            ->where('UtSenha', $credentials['UtSenha']) // sem hash
            ->first();

        if (!$user) {
            return back()->withErrors([
                'UtCodigo' => 'Utilizador ou senha inválidos.',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        // Verificando se o usuário está autenticado
        if (Auth::check()) {



            // Verificando diretamente o usuário autenticado
            $authenticatedUser = Auth::user();  // O usuário logado
            $agencia = TKxAgenciaModel::where('OfCodigo', $authenticatedUser->UtAgencia)->first();

            session(['agencia_principal' => $agencia->OfNombre]);
            $basesOperacaoIDs = explode(',', $agencia->BasesOperacao);

            $basesOperacionais = TKxAgenciaModel::whereIn('OfIdentificador', $basesOperacaoIDs)->get(['OfCodigo', 'OfIdentificador', 'OfNombre']); // pegar só os campos necessários


            // Exibindo os dados
            session(['bases_operacionais' => $basesOperacionais->toArray()]);

            //dd(session()->all());
            //  dd( $resultagencia_user);
            return redirect()->intended('/dashboard');

        } else {
            return back()->withErrors([
                'UtCodigo' => 'Falha na autenticação.',
            ]);
        }



    }
    public function carregamentoInicial(Request $request)
    {
        $filtros = $request->only(['search', 'start_date', 'end_date']);


        $authenticatedUser = Auth::user();
        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $authenticatedUser->UtAgencia)->first();

        $Bases = "'" . $resultagencia_user->BasesOperacao . "'";

        $lista_banco = TKxBancoModel::getBancos();
        $lista_bancos_contas = TKxBancoContaModel::getBancosContas();
        $lista_actividade_economica = TKxCodigoCaeModel::getActividadeEconomica();

        $BasesOperacao = explode(',', $resultagencia_user->BasesOperacao);
        $lista_nes_grupo = TKxExtratoModel::getNecesidadesGrupo();
        $lista_nes_tipo = TKxExtratoModel::getNecesidadesTipo();

        $listaprdt = TKxClProdutoModel::getProdutosDesembolsos();

        $lista_produtos = TKxClProdutoModel::getProdutos();
        $lista_banco = TKxBancoModel::getBancos();
        $lista_bancos_contas = TKxBancoContaModel::getBancosContas();
        $lista_das_formaspagamento = TKxClTipopagamentoModel::getFormasDePamentos();

        $QtdRegistosComprovativos = 0;
        $QtdValorRegistosComprovativos = 0;

        $QtdRegistosRecuperacoes = 0;
        $QtdValorRegistosRecuperacoes = 0;

        $QtdRegistosDesembosos = 0;
        $QtdValorRegistosDesembosos = 0;


        // DCF

        $TotaldeRegistossemParacer = 0;
        $TotalValordeRegistossemParacer = 0;

        $TotaldeRegistosRespondidos = 0;
        $TotalValordeRegistosRespondidos = 0;

        $TotaldeReconciliaNaoFinalizado = 0;
        $TotalValorReconciliaNaoFinalizado = 0;

        $cpvtDFC = null;
        $cpvtDFC2 = null;
        $cpvtDFC3 = null;
        $cpvtDOP = null;
        $extrato = null;
        $cpvtRecupe = null;



        $TipoComprovativo = [
            'G' => 'G/',
            'I' => 'I/'
        ];
        $hoje = date('Y-m-d 00:00:00');

        if ($request->search == 1) {

            $DataInicio = date("Y-m-d 00:00:00", strtotime($request->start_date));
            $DataFim = date("Y-m-d 23:59:00", strtotime($request->end_date));

            $cpvtDFC = ComprovativoModel::whereIn('BaseOperacao', $BasesOperacao)->whereDate('CiFecha', '>=', $DataInicio)->whereDate('CiFecha', '<=', $DataFim)->where('idestado', 1)->where('Eliminado', 0);
            $cpvtDFC2 = ComprovativoModel::whereIn('BaseOperacao', $BasesOperacao)->whereDate('CiFecha', '>=', $DataInicio)->whereDate('CiFecha', '<=', $DataFim)->where('idestado', 8)->where('Eliminado', 0);
            $cpvtDFC3 = ComprovativoModel::whereIn('BaseOperacao', $BasesOperacao)->whereDate('CiFecha', '>=', $DataInicio)->whereDate('CiFecha', '<=', $DataFim)->where('idestado', '<>', 8)->where('idestado', '<>', 1)->where('Eliminado', 0);

            $cpvtDOP = ComprovativoModel::whereIn('BaseOperacao', $BasesOperacao)->whereDate('CiFecha', '>=', $DataInicio)->whereDate('CiFecha', '<=', $DataFim)->where('Eliminado', 0);
            $extrato = TKxExtratoModel::whereIn('BaseOperacao', $BasesOperacao)->whereDate('CiFecha', '>=', $DataInicio)->whereDate('CiFecha', '<=', $DataFim)->where('Eliminado', 0);

            $cpvtRecupe = RecuperacaoModel::whereIn('BaseOperacao', $BasesOperacao)->whereDate('CiFecha', '>=', $DataInicio)->whereDate('CiFecha', '<=', $DataFim)->where('id_estado','<>', 6)->where('Eliminado', 0);




        } else {
            $cpvtDOP = ComprovativoModel::whereIn('BaseOperacao', $BasesOperacao)->whereDate('CiFecha', $hoje)->where('Eliminado', 0);

            $cpvtRecupe = RecuperacaoModel::whereIn('BaseOperacao', $BasesOperacao)->whereDate('CiFecha', $hoje)->where('id_estado', '<>', 6)->where('Eliminado', 0);
            $extrato = TKxExtratoModel::whereIn('BaseOperacao', $BasesOperacao)->whereDate('CiFecha', $hoje)->where('Eliminado', 0);

            $cpvtDFC = ComprovativoModel::whereIn('BaseOperacao', $BasesOperacao)->whereDate('CiFecha', $hoje)->where('idestado', 1)->where('Eliminado', 0);
            $cpvtDFC2 = ComprovativoModel::whereIn('BaseOperacao', $BasesOperacao)->whereDate('CiFecha', $hoje)->where('idestado', 8)->where('Eliminado', 0);

            $cpvtDFC3 = ComprovativoModel::whereIn('BaseOperacao', $BasesOperacao)->whereDate('CiFecha', $hoje)->where('idestado', '<>', 8)->where('idestado', '<>', 1)->where('Eliminado', 0);


        }


        $TotalValordeRegistossemParacer = $cpvtDFC->sum('BuMontante');
        $TotaldeRegistossemParacer = $cpvtDFC->count();

        $TotalValordeRegistosRespondidos = $cpvtDFC2->sum('BuMontante');
        $TotaldeRegistosRespondidos = $cpvtDFC2->count();

        $TotalValorReconciliaNaoFinalizado = $cpvtDFC3->sum('BuMontante');
        $TotaldeReconciliaNaoFinalizado = $cpvtDFC3->count();


        $QtdRegistosComprovativos = $cpvtDOP->count();
        $QtdValorRegistosComprovativos = $cpvtDOP->sum('BuMontante');

        $QtdRegistosRecuperacoes = $cpvtRecupe->count();
        $QtdValorRegistosRecuperacoes = $cpvtRecupe->sum('ReBuMontante');

        $QtdRegistosDesembosos = $extrato->count();
        $QtdValorRegistosDesembosos = $extrato->sum('ValorDoCredito');


        $BasesOperacaoAgencias = TKxAgenciaModel::whereIn('OfIdentificador', $BasesOperacao)->get();



        return Inertia::render('Dashboard', [

            'produtosextratos' => $listaprdt,
            'produtos' => $lista_produtos,
            'formaspagamentos' => $lista_das_formaspagamento,
            'bancos' => $lista_banco,
            'contas' => $lista_bancos_contas,
            'tipocomprovativos' => $TipoComprovativo,
            'lista_nes_grupo' => $lista_nes_grupo,
            'lista_nes_tipo' => $lista_nes_tipo,
            'lista_bancos_contas' => $lista_bancos_contas,
            'lista_actividade_economica' => $lista_actividade_economica,
            'BasesOperacao' => explode(',', $resultagencia_user->BasesOperacao),
            'bases' => $BasesOperacaoAgencias,
            'QtdRegistosComprovativos' => $QtdRegistosComprovativos,
            'QtdValorRegistosComprovativos' => $QtdValorRegistosComprovativos,
             'QtdRegistosDesembosos' => $QtdRegistosDesembosos,
            'QtdValorRegistosDesembosos' => $QtdValorRegistosDesembosos,
            'TotaldeRegistossemParacer' => $TotaldeRegistossemParacer,
            'TotalValordeRegistossemParacer' => $TotalValordeRegistossemParacer,
            'TotaldeRegistosRespondidos' => $TotaldeRegistosRespondidos,
            'TotalValordeRegistosRespondidos' => $TotalValordeRegistosRespondidos,
            'TotaldeReconciliaNaoFinalizado' => $TotaldeReconciliaNaoFinalizado,
            'TotalValorReconciliaNaoFinalizado' => $TotalValorReconciliaNaoFinalizado,
            'QtdRegistosRecuperacoes' => $QtdRegistosRecuperacoes,
            'QtdValorRegistosRecuperacoes' => $QtdValorRegistosRecuperacoes
        ]);
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login'); // redireciona para /
    }
    public function cadastrar(Request $request)
    {


        //Validação
        $dados = $request->validate([
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required']
        ]);

        //Registar
        $user = User::create($dados);

        //Login
        Auth::login($user);

        //  dd($request);

        return redirect()->route('Home');
    }


}
