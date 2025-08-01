<?php

namespace App\Http\Controllers;

use App\Models\{
    ComprovativoModel,
    RecuperacaoModel,
    TKuPendentesModel,
    TKxAgenciaModel,
    TKxBancoContaModel,
    TKxBancoModel,
    TKxClProdutoModel,
    TKxClTipopagamentoModel,
    TKxCodigoCaeModel,
    TKxExtratoModel,
    TKxUsUtilizadorModel,
    User
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'UtCodigo' => ['required', 'string'],
            'UtSenha' => ['required', 'string'],
        ]);

        $user = TKxUsUtilizadorModel::where('UtCodigo', $credentials['UtCodigo'])
            ->where('UtSenha', $credentials['UtSenha'])
            ->where('activo', 1)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'UtCodigo' => 'Credenciais inválidas.',
            ]);
        }

        Auth::login($user, $request->remember ?? false);

        // Debug - verifique se a sessão está sendo criada
      ///  \Log::info('Session ID after login:', ['session_id' => session()->getId()]);
$this->loadUserSessionData($user);
        return redirect()->intended(route('dashboard'));
    }

    protected function loadUserSessionData($user)
    {
        // Carrega todos os dados necessários da agência
        $agencia = TKxAgenciaModel::where('OfCodigo', $user->UtAgencia)
            ->first(); // Removi o select específico para pegar todos os campos

        if ($agencia) {
            $basesOperacionais = TKxAgenciaModel::whereIn(
                'OfIdentificador',
                explode(',', $agencia->BasesOperacao)
            )
                ->get(['OfCodigo', 'OfIdentificador', 'OfNombre']);

            // Armazena mais dados da agência na sessão
            session([
                'bases_operacionais' => $basesOperacionais->toArray(),
                'agencia_principal' => $agencia->OfNombre,
                'agencia_data' => [ // Adiciona um array com todos os dados relevantes
                    'codigo' => $agencia->OfCodigo,
                    'nombre' => $agencia->OfNombre,
                    'identificador' => $agencia->OfIdentificador,
                    'bases_operacao' => $agencia->BasesOperacao
                ]
            ]);
        }
    }

    public function carregamentoInicial(Request $request)
    {




        $authenticatedUser = Auth::user();
        $agenciaUser = TKxAgenciaModel::where('OfCodigo', $authenticatedUser->UtAgencia)
            ->first(['BasesOperacao']);

        if (!$agenciaUser) {
            return redirect()->back()->withErrors(['error' => 'Agência não encontrada']);
        }

        $basesOperacao = explode(',', $agenciaUser->BasesOperacao);

        // Carregar dados estáticos uma única vez
        $staticData = $this->loadStaticData();

        // Processar dados dinâmicos
        $dynamicData = $this->processDynamicData($request, $basesOperacao);

        // Combinar todos os dados para a view


        $viewData = array_merge($staticData, $dynamicData, [
            'auth' => ['user' => Auth::user()], // Adiciona o usuário no formato esperado pelo frontend
            'BasesOperacao' => $basesOperacao,
            'bases' => TKxAgenciaModel::whereIn('OfIdentificador', $basesOperacao)->get(),
            'agencia_principal' => session('agencia_principal')
        ]);

        return Inertia::render('Dashboard', $viewData);
    }

    protected function loadStaticData()
    {

        return [
            'produtosextratos' => TKxClProdutoModel::getProdutosDesembolsos(),
            'produtos' => TKxClProdutoModel::getProdutos(),
            'formaspagamentos' => TKxClTipopagamentoModel::getFormasDePamentos(),
            'bancos' => TKxBancoModel::getBancos(),
            'contas' => TKxBancoContaModel::getBancosContas(),
            'tipocomprovativos' => ['G' => 'G/', 'I' => 'I/'],
            'lista_nes_grupo' => TKxExtratoModel::getNecesidadesGrupo(),
            'lista_nes_tipo' => TKxExtratoModel::getNecesidadesTipo(),
            'lista_bancos_contas' => TKxBancoContaModel::getBancosContas(),
            'lista_banco' => TKxBancoModel::getBancos(),
            'lista_actividade_economica' => TKxCodigoCaeModel::getActividadeEconomica(),
            'agencia_principal' => session('agencia_principal')

        ];
    }

    protected function processDynamicData(Request $request, array $basesOperacao)
    {
        $dateFilter = $this->getDateFilter($request);
        $hoje = Carbon::today()->format('Y-m-d 00:00:00');

        // Dados de comprovativos
        $comprovativosData = $this->getComprovativosData($basesOperacao, $dateFilter, $hoje);

        // Dados de recuperações
        $recuperacoesData = $this->getRecuperacoesData($basesOperacao, $dateFilter, $hoje);

        // Dados de extratos
        $extratosData = $this->getExtratosData($basesOperacao, $dateFilter, $hoje);

        // Dados pendentes
        $pendentesData = $this->getPendentesData($basesOperacao);

        return array_merge(
            $comprovativosData,
            $recuperacoesData,
            $extratosData,
            $pendentesData
        );
    }

    protected function getDateFilter(Request $request)
    {
        if ($request->search == 1) {
            return [
                'start' => Carbon::parse($request->start_date)->startOfDay(),
                'end' => Carbon::parse($request->end_date)->endOfDay()
            ];
        }
        return null;
    }

    protected function getComprovativosData(array $basesOperacao, ?array $dateFilter, string $hoje)
    {
        $query = ComprovativoModel::whereIn('BaseOperacao', $basesOperacao)
            ->where('Eliminado', 0);

        if ($dateFilter) {
            $query->whereBetween('CiFecha', [$dateFilter['start'], $dateFilter['end']]);

            $cpvtDFC = (clone $query)->where('idestado', 1);
            $cpvtDFC2 = (clone $query)->where('idestado', 8);
            $cpvtDFC3 = (clone $query)->whereNotIn('idestado', [1, 8]);
        } else {
            $query->whereDate('CiFecha', $hoje);

            $cpvtDFC = (clone $query)->where('idestado', 1);
            $cpvtDFC2 = (clone $query)->where('idestado', 8);
            $cpvtDFC3 = (clone $query)->whereNotIn('idestado', [1, 8]);
        }

        return [
            'QtdRegistosComprovativos' => $query->count(),
            'QtdValorRegistosComprovativos' => $query->sum('BuMontante'),
            'TotaldeRegistossemParacer' => $cpvtDFC->count(),
            'TotalValordeRegistossemParacer' => $cpvtDFC->sum('BuMontante'),
            'TotaldeRegistosRespondidos' => $cpvtDFC2->count(),
            'TotalValordeRegistosRespondidos' => $cpvtDFC2->sum('BuMontante'),
            'TotaldeReconciliaNaoFinalizado' => $cpvtDFC3->count(),
            'TotalValorReconciliaNaoFinalizado' => $cpvtDFC3->sum('BuMontante'),
        ];
    }

    protected function getRecuperacoesData(array $basesOperacao, ?array $dateFilter, string $hoje)
    {
        $query = RecuperacaoModel::whereIn('BaseOperacao', $basesOperacao)
            ->where('id_estado', '<>', 6)
            ->where('Eliminado', 0);

        if ($dateFilter) {
            $query->whereBetween('CiFecha', [$dateFilter['start'], $dateFilter['end']]);
        } else {
            $query->whereDate('CiFecha', $hoje);
        }

        return [
            'QtdRegistosRecuperacoes' => $query->count(),
            'QtdValorRegistosRecuperacoes' => $query->sum('ReBuMontante'),
        ];
    }

    protected function getExtratosData(array $basesOperacao, ?array $dateFilter, string $hoje)
    {
        $query = TKxExtratoModel::whereIn('BaseOperacao', $basesOperacao)
            ->where('Eliminado', 0);

        if ($dateFilter) {
            $query->whereBetween('CiFecha', [$dateFilter['start'], $dateFilter['end']]);
        } else {
            $query->whereDate('CiFecha', $hoje);
        }

        return [
            'QtdRegistosDesembosos' => $query->count(),
            'QtdValorRegistosDesembosos' => $query->sum('ValorDoCredito'),
        ];
    }

    protected function getPendentesData(array $basesOperacao)
    {
        $cpvtPendentes = TKuPendentesModel::whereIn('BaseOperacao', $basesOperacao)
            ->where('Tipo', 'R');

        return [
            'TotalValordeReembolsosPendentes' => $cpvtPendentes->sum('montante'),
            'TotaldeRegistosdeReembolsosPendentes' => $cpvtPendentes->count(),
        ];
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }


}
