<?php

use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\LeaderAuthController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\MagicLinkController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

// Magic link (rate limited) — rota curta `/m/{token}` e legado `/auth/acesso?t=...`
Route::middleware('throttle:magic-link')->group(function () {
    Route::get('/m/{token}', [MagicLinkController::class, 'consume'])->name('magic-link.short');
    Route::get('/auth/acesso', [MagicLinkController::class, 'consume'])->name('magic-link.consume');
});
Route::get('/auth/link-invalido', [MagicLinkController::class, 'invalid'])->name('magic-link.invalid');

// Admin 2FA (autenticado, fora do Filament)
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.two-factor.')->group(function () {
    Route::get('/dois-fatores/configurar', [TwoFactorController::class, 'setup'])->name('setup');
    Route::post('/dois-fatores/confirmar', [TwoFactorController::class, 'confirm'])->name('confirm');
    Route::get('/dois-fatores/verificar', [TwoFactorController::class, 'challenge'])->name('challenge');
    Route::post('/dois-fatores/verificar', [TwoFactorController::class, 'verify'])->name('verify');
    Route::post('/dois-fatores/desativar', [TwoFactorController::class, 'disable'])->name('disable');
});

// Login do líder (email + senha)
Route::prefix('lider')->name('leader.')->group(function () {
    Route::get('/login', [LeaderAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [LeaderAuthController::class, 'login'])->middleware('throttle:admin-login')->name('login.attempt');
});

// Área do líder (autenticada)
Route::middleware('auth.leader')->prefix('lider')->name('leader.')->group(function () {
    Route::get('/dashboard', [LeaderController::class, 'dashboard'])->name('dashboard');
    Route::get('/colaborador/{id}/postura', [LeaderController::class, 'collaboratorScore'])->name('collaborator.score');
    Route::get('/relatorio/pdf', [\App\Http\Controllers\ReportController::class, 'downloadPdf'])->name('report.pdf');
    Route::middleware('throttle:invite')->group(function () {
        Route::get('/convidar', [\App\Http\Controllers\LeaderInviteController::class, 'index'])->name('invite.index');
        Route::post('/convidar', [\App\Http\Controllers\LeaderInviteController::class, 'store'])->name('invite.store');
        Route::post('/convidar/{collaborator}/reenviar', [\App\Http\Controllers\LeaderInviteController::class, 'resend'])->name('invite.resend');
        Route::post('/convidar/{collaborator}/gerar-link', [\App\Http\Controllers\LeaderInviteController::class, 'generateLink'])->name('invite.generate-link');
    });
    Route::post('/logout', [LeaderAuthController::class, 'logout'])->name('logout');
});

// Área do colaborador
Route::middleware('auth.collaborator')->prefix('treinamento')->name('training.')->group(function () {
    Route::get('/', [CollaboratorController::class, 'index'])->name('index');
    Route::get('/boas-vindas', [CollaboratorController::class, 'welcome'])->name('welcome');
    Route::get('/como-funciona', [CollaboratorController::class, 'howItWorks'])->name('how-it-works');
    Route::post('/iniciar', [CollaboratorController::class, 'startJourney'])->name('start');
    Route::get('/transicao/{scenario}', [CollaboratorController::class, 'transition'])->name('transition');
    Route::get('/cenario/{scenario}', [CollaboratorController::class, 'show'])->name('show');
    Route::post('/resposta', [CollaboratorController::class, 'answer'])->name('answer');
    Route::get('/concluido', [CollaboratorController::class, 'completed'])->name('completed');
    Route::post('/logout', [CollaboratorController::class, 'logout'])->name('logout');
});
