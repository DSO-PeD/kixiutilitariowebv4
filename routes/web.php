<?php

use App\Http\Controllers\ComprovativosController;
use App\Http\Controllers\CpvtReconciliacaoController;
use App\Http\Controllers\RecuperacaoController;
use App\Http\Controllers\ReportDomPDFController;
use App\Http\Controllers\TKxExtratoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

//Proteger o dashboard:
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'carregamentoInicial'])->name('dashboard');

    /*Route::get('/dashboard', function () {
        // Recupera os dados da session
        $bases_opecionais = session('bases_opecionais');
        $agencia_p = session('agencia_principal');

        $user = auth()->user();  // Exemplo de dados do usuário
        $flashMessage = session('flash_message');  // Exemplo de flash message

        // Passa para o Vue via Inertia
        return Inertia::render('Dashboard', [
            'bases_opecionais' => $bases_opecionais,
            'agencia' =>$agencia_p,
            'user' => $user,
            'flashMessage' => $flashMessage,  // Passando uma mensagem flash como exemplo
            'errors' => session('errors'),  // Passando erros se existirem
            'auth' => auth()->user(),  // Passando dados de autenticação
        ]);
    });*/

    Route::get('/comprovativos', [ComprovativosController::class, 'viewComprovativos'])->name('comprovativos');
    Route::get('/reconciliacao', [CpvtReconciliacaoController::class, 'viewComprovativosReconlicacao'])->name('comprovativosreco');
    Route::get('/listarEstadosDCF', [CpvtReconciliacaoController::class, 'listarEstadosReconciliacao']);
    Route::get('/extratos', [TKxExtratoController::class, 'viewExtrato'])->name('extratos');
    Route::post('/guardar-comprovativo', [ComprovativosController::class, 'guardar']);

    Route::post('/guardar-extrato', [TKxExtratoController::class, 'guardarDataExtrato']);
    Route::post('/comprovativos/{id}/finalizaraeliminacao', [ComprovativosController::class, 'finalizaraeliminacao']);
    Route::post('/criar-referencia', [TKxExtratoController::class, 'criarReferencia']);
    Route::post('/validar-comprovativo', [CpvtReconciliacaoController::class, 'validarComprovativo']);
    Route::get('/reports/extrato/{id}', [ReportDomPDFController::class, 'emitirRelatorioCalculoDesembolso'])->name('report_extrato_new');

    Route::get('/recuperacoes', [RecuperacaoController::class, 'viewRecuperacoes'])->name('recuperacoes');
    Route::post('/guardar-recuperacao', [RecuperacaoController::class, 'guardar']);
    Route::get('/exportar-recuperacao', [RecuperacaoController::class, 'alteracaoEstadoExportar']);
    Route::get('/listarEstados', [RecuperacaoController::class, 'listarEstados']);
    Route::get('/listarAgencias', [RecuperacaoController::class, 'listarAgencias']);
    Route::get('/confirmarRecuperacao', [RecuperacaoController::class, 'confirmarRecuperacao']);
});

Route::inertia('/novoutilizador', 'Auth/Utilizador');
Route::post('/novoutilizador', [AuthController::class, 'cadastrar']);



