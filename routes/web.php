<?php

use App\Http\Controllers\MagicLinkController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\CollaboratorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

// Magic link consumption (público)
Route::get('/auth/acesso', [MagicLinkController::class, 'consume'])->name('magic-link.consume');
Route::get('/auth/link-invalido', [MagicLinkController::class, 'invalid'])->name('magic-link.invalid');

// Área do líder
Route::middleware('auth.leader')->prefix('lider')->name('leader.')->group(function () {
    Route::get('/dashboard', [LeaderController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [LeaderController::class, 'logout'])->name('logout');
});

// Área do colaborador
Route::middleware('auth.collaborator')->prefix('treinamento')->name('training.')->group(function () {
    Route::get('/', [CollaboratorController::class, 'index'])->name('index');
    Route::get('/cenario/{scenario}', [CollaboratorController::class, 'show'])->name('show');
    Route::post('/resposta', [CollaboratorController::class, 'answer'])->name('answer');
    Route::get('/concluido', [CollaboratorController::class, 'completed'])->name('completed');
    Route::post('/logout', [CollaboratorController::class, 'logout'])->name('logout');
});
