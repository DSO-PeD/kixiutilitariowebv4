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


    Route::get('/comprovativos', [ComprovativosController::class, 'viewComprovativos'])->name('comprovativos');
    Route::get('/reconciliacao', [CpvtReconciliacaoController::class, 'viewComprovativosReconlicacao'])->name('comprovativosreco');
    Route::get('/listarEstadosDCF', [CpvtReconciliacaoController::class, 'listarEstadosReconciliacao']);
    Route::get('/listarCpvtDetalheDCF', [CpvtReconciliacaoController::class, 'listarDetalhesComprovativosDCF']);
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



Route::get('/carregarcomprovativos', [ComprovativosController::class, 'carregaComprovativosKP'])->name('comprovativos_kxu');
Route::get('/carregarextratos', [TKxExtratoController::class, 'carregaExtratosKP'])->name('extrato_kxu');





