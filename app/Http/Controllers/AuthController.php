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
    TKxUsUtilizadorModel
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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

        // Procurar utilizador ativo
        $user = TKxUsUtilizadorModel::where('UtCodigo', $credentials['UtCodigo'])
            ->where('UtSenha', $credentials['UtSenha'])
            ->where('activo', 1)
            ->first();

        // Validação de credenciais (usando Hash::check)
        if (!$user) {
            return back()->withErrors([
                'UtCodigo' => 'Credenciais inválidas.',
            ]);
        }

        // Login do utilizador
        Auth::login($user, $request->boolean('remember'));

        // Previne session fixation
        $request->session()->regenerate();

        // Carregar dados de sessão específicos do utilizador
        $this->loadUserSessionData($user);

        return redirect()->intended(route('dashboard'));
    }
    public function loginMobile(Request $request)
    {
        $utilizador = $request->input('utilizador');
        $password = $request->input('password');

        $user = TKxUsUtilizadorModel::where('UtCodigo', $utilizador)
            ->where('UtSenha', $password)
            ->where('activo', 1)
            ->first();

        if ($user) {
            $agencia = TKxAgenciaModel::where('OfCodigo', $user->UtAgencia)->first();

            $basesOperacao = explode(',', $agencia->BasesOperacao);


            $basesOperacionais = TKxAgenciaModel::whereIn(
                'OfIdentificador',
                $basesOperacao
            )->get(['OfCodigo', 'OfIdentificador', 'OfNombre']);

            $ProdutosPrestacao = TKxClProdutoModel::where(
                'TipoProduto'               ,
                '=',
                'L'
            )->where('Estado', 1)->get(['Metodologia', 'PoAgrupado']);

             $ProdutosPoupancas= TKxClProdutoModel::where(
                'TipoProduto'               ,
                '=',
                'S'
            )->where('Estado', 1)->get(['Metodologia', 'PoAgrupado']);


            $dateFilter = $this->getDateFilter($request);

            $FormasDePagamento = TKxClTipopagamentoModel::getFormasDePamentos();








            $hoje = Carbon::today()->format('Y-m-d 00:00:00');



            $query = ComprovativoModel::whereIn('BaseOperacao', $basesOperacao)->where('Eliminado', 0);
            $query->whereDate('CiFecha', $hoje);
            $cpvtDFC = (clone $query)->where('idestado', 19);
            $cpvtDFC2 = (clone $query)->where('idestado', 8);
            $cpvtDFC3 = (clone $query)->whereIn('idestado', [9, 11, 13, 20]);

            $response = [
                'status' => 'SUCCESS',
                'message' => 'Login efectuado com sucesso.',
                'data' => [
                    'utilizador' => [
                        'UtCodigo' => $user->UtCodigo,
                        'UtNome' => $user->UtNome,
                        'UtFuncao' => $user->UtFuncao,
                        'UtAgencia' => $user->UtAgencia,
                    ],
                    'agencia' => $agencia ? [
                        'OfCodigo' => $agencia->OfCodigo,
                        'OfNombre' => $agencia->OfNombre,
                        'OfIdentificador' => $agencia->OfIdentificador,
                        'BasesOperacao' => $agencia->BasesOperacao,
                    ] : null,
                    'dadosestatisticos' => [
                        'QtdRegistosComprovativos' => $query->count(),
                        'QtdValorRegistosComprovativos' => $query->sum('BuMontante'),
                        'TotaldeRegistossemParacer' => $cpvtDFC->count(),
                        'TotalValordeRegistossemParacer' => $cpvtDFC->sum('BuMontante'),
                        'TotaldeRegistosRespondidos' => $cpvtDFC2->count(),
                        'TotalValordeRegistosRespondidos' => $cpvtDFC2->sum('BuMontante'),
                        'TotaldeReconciliaNaoFinalizado' => $cpvtDFC3->count(),
                        'TotalValorReconciliaNaoFinalizado' => $cpvtDFC3->sum('BuMontante'),
                    ],
                    'basesOperacionais' => $basesOperacionais,
                    'ProdutosPrestacao' => $ProdutosPrestacao,
                    'ProdutosPoupancas' => $ProdutosPoupancas,
                    'FormasDePagamento'=>  $FormasDePagamento

                ]
            ];

            return response()->json($response, 200);
        } else {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Credenciais inválidas!'
            ], 401);
        }
    }

    protected function loadUserSessionData($user)
    {
        $agencia = TKxAgenciaModel::where('OfCodigo', $user->UtAgencia)->first();

        if ($agencia) {
            $basesOperacionais = TKxAgenciaModel::whereIn(
                'OfIdentificador',
                explode(',', $agencia->BasesOperacao)
            )->get(['OfCodigo', 'OfIdentificador', 'OfNombre']);

            // Armazenar na sessão com prefixo único (evita conflito com outras apps)
            session([
                'utilitario_v9.bases_operacionais' => $basesOperacionais->toArray(),
                'utilitario_v9.agencia_principal' => $agencia->OfNombre,
                'utilitario_v9.agencia_data' => [
                    'codigo' => $agencia->OfCodigo,
                    'nombre' => $agencia->OfNombre,
                    'identificador' => $agencia->OfIdentificador,
                    'bases_operacao' => $agencia->BasesOperacao,
                ],
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


        $staticData = $this->loadStaticData();
        $dynamicData = $this->processDynamicData($request, $basesOperacao);

        $viewData = array_merge($staticData, $dynamicData, [
            'auth' => ['user' => Auth::user()],
            'BasesOperacao' => $basesOperacao,
            'bases' => TKxAgenciaModel::whereIn('OfIdentificador', $basesOperacao)->get(),
            'agencia_principal' => session('utilitario_v9.agencia_principal'),
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
            'agencia_principal' => session('utilitario_v9.agencia_principal'),
        ];
    }

    protected function processDynamicData(Request $request, array $basesOperacao)
    {
        $dateFilter = $this->getDateFilter($request);
        $hoje = Carbon::today()->format('Y-m-d 00:00:00');

        $comprovativosData = $this->getComprovativosData($basesOperacao, $dateFilter, $hoje);
        $recuperacoesData = $this->getRecuperacoesData($basesOperacao, $dateFilter, $hoje);
        $extratosData = $this->getExtratosData($basesOperacao, $dateFilter, $hoje);
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
            $cpvtDFC = (clone $query)->where('idestado', 19);
            $cpvtDFC2 = (clone $query)->where('idestado', 8);
            $cpvtDFC3 = (clone $query)->whereIn('idestado', [9, 11, 13, 20]);
        } else {
            $query->whereDate('CiFecha', $hoje);
            $cpvtDFC = (clone $query)->where('idestado', 19);
            $cpvtDFC2 = (clone $query)->where('idestado', 8);
            $cpvtDFC3 = (clone $query)->whereIn('idestado', [9, 11, 13, 20]);


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

        return redirect('/');
    }

    public function listarUtilizadores(Request $request)
    {
        $query = DB::table('tkxusutilizador as us')
                    ->leftjoin('sessions_utilitario_v9 as su','su.user_id','=','us.UtCodigo') 
                    ->select(
                        'us.UtCodigo',
                        'us.UtNome',
                        'us.UtFuncao',
                        'su.user_id',
                        DB::raw('CASE WHEN su.user_id IS NOT NULL AND su.last_activity >= ' . now()->subMinutes(15)->getTimestamp() . ' THEN 1 ELSE 0 END as logado')
                    );
        
        //Filtro por nome
        if ($request->filled('nome')) {
            $query->where('us.UtNome', 'like', '%' . $request->nome . '%');
        }

        // Filtro por status
        if ($request->filled('status')) {
            if ($request->status === 'logado') {
                $query->whereNotNull('su.user_id')
                    ->where('su.last_activity', '>=', now()->subMinutes(15)->getTimestamp());
            } elseif ($request->status === 'nao_logado') {
                $query->where(function ($q) {
                    $q->whereNull('su.user_id')
                    ->orWhere('su.last_activity', '<', now()->subMinutes(15)->getTimestamp());
                });
            }
        }

        $utilizadores = $query->orderBy('us.UtNome','asc')
                        ->paginate(48)
                        ->withQueryString(); //Manter os filtros na URL durante a paginação

        //Pegar o total de utilizadores logados e não logados
        $totalLogados = DB::table('tkxusutilizador as us')
                            ->leftJoin('sessions_utilitario_v9 as su', 'su.user_id', '=', 'us.UtCodigo')
                            ->whereNotNull('su.user_id')
                            ->where('su.last_activity', '>=', now()->subMinutes(15)->getTimestamp())
                            ->count();

        $totalNaoLogados = DB::table('tkxusutilizador as us')
                            ->leftJoin('sessions_utilitario_v9 as su', 'su.user_id', '=', 'us.UtCodigo')
                            ->where(function ($q) {
                                $q->whereNull('su.user_id')
                                ->orWhere('su.last_activity', '<', now()->subMinutes(15)->getTimestamp());
                            })
                            ->count();
        
        
        return Inertia::render('ListaUtilizadores', [
            'utilizadores' => $utilizadores,
            'filters' => $request->only(['nome','status']),
            'totalLogados' => $totalLogados,
            'totalNaoLogados' => $totalNaoLogados,
        ]);
    }

    public function verUtilizador($UtCodigo){
        $user = DB::table('tkxusutilizador as us')
                    ->join('tkxagencias as ag','ag.OfCodigo','=','us.UtAgencia')
                    ->select('us.UtCodigo','us.UtNome','us.UtFuncao','us.UtFuncao','us.activo','ag.OfNombre')
                    ->where('UtCodigo',$UtCodigo)
                    ->first();
                    
        $permissoesUser = DB::table('tkxusutilizador_permissions as up')
                            ->join('permissions as p', 'p.id', '=', 'up.permission_id')
                            ->where('up.UtCodigo', $UtCodigo)
                            ->select('p.id','p.name','p.label')
                            ->get();

        $user->permissoes = $permissoesUser;
                            
        /** Todas permissões */
        $permissoes = DB::table('permissions as p')
                            ->select('p.id','p.name','p.label')
                            ->get();
        
        return Inertia::render('VerUtilizador', [
            'utilizador' => $user,
            'permissions' => $permissoes
        ]);
    }

    public function atribuirPermissionUser(Request $request, $UtCodigo){

        foreach ($request->permission_ids as $permissionId) {
            DB::table('tkxusutilizador_permissions')->updateOrInsert(
                [
                    'UtCodigo' => $UtCodigo,
                    'permission_id' => $permissionId,
                ],
                []
            );
        }

        return back()->with('success', 'Permissão atribuída com sucesso.');
    }
    
    public function removerPermissionUser(Request $request, $UtCodigo){

        DB::table('tkxusutilizador_permissions')
            ->where('UtCodigo', $UtCodigo)
            ->whereIn('permission_id', $request->permission_ids)
            ->delete();

        return back()->with('success', 'Permissão removida com sucesso.');
    }
}
