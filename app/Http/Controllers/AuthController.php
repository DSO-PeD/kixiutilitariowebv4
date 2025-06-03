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
    public function carregamentoInicial()
    {

        $authenticatedUser = Auth::user();
        $resultagencia_user = TKxAgenciaModel::where('OfCodigo', '=', $authenticatedUser->UtAgencia)->first();

        $Bases = "'" . $resultagencia_user->BasesOperacao . "'";

        $lista_banco = TKxBancoModel::getBancos();
        $lista_bancos_contas = TKxBancoContaModel::getBancosContas();
        $lista_actividade_economica = TKxCodigoCaeModel::getActividadeEconomica();

        $BasesOperacao = explode(',', $resultagencia_user->BasesOperacao);
        $lista_nes_grupo = TKxExtratoModel::getNecesidadesGrupo();
        $lista_nes_tipo = TKxExtratoModel::getNecesidadesTipo();

        $listaprdt= TKxClProdutoModel::getProdutosDesembolsos();

        $lista_produtos = TKxClProdutoModel::getProdutos();
        $lista_banco = TKxBancoModel::getBancos();
        $lista_bancos_contas = TKxBancoContaModel::getBancosContas();
        $lista_das_formaspagamento = TKxClTipopagamentoModel::getFormasDePamentos();

        $TipoComprovativo = [
            'G' => 'G/',
            'I' => 'I/'
        ];
        $hoje = date('Y-m-d');
        $QtdRegistosComprovativosHoje = ComprovativoModel::whereIn('BaseOperacao', $BasesOperacao)->whereDate('CiFecha', $hoje)->count();
        $QtdRegistosRecuperacoesHoje = RecuperacaoModel::whereIn('BaseOperacao', $BasesOperacao)->whereDate('CiFecha', $hoje)->count();
        $ValorTotalRegistosRecuperacoesHoje = TKxExtratoModel::whereIn('BaseOperacao', $BasesOperacao)->whereDate('CiFecha', $hoje)->sum('ValorDoCredito');
        $ValorTotalRegistosRecuperacoesHoje =number_format($ValorTotalRegistosRecuperacoesHoje, 2, ',', '.');
        $BasesOperacaoAgencias = TKxAgenciaModel::whereIn('OfIdentificador', $BasesOperacao)->get();



        return Inertia::render('Dashboard', [

            'produtosextratos'=> $listaprdt,
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
            'QtdRegistosComprovativosHoje' => $QtdRegistosComprovativosHoje,
            'QtdRegistosRecuperacoesHoje' => $QtdRegistosRecuperacoesHoje,
            'ValorTotalRegistosRecuperacoesHoje'=>$ValorTotalRegistosRecuperacoesHoje

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
