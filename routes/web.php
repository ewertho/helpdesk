<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CannedReplyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');
    // Limite por minuto contra força bruta de senha e cadastro em massa.
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::redirect('/', '/tickets');

    Route::resource('tickets', TicketController::class)->only(['index', 'create', 'store', 'show', 'update']);
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
    Route::post('/tickets/{ticket}/rating', [TicketController::class, 'rate'])->name('tickets.rate');
    Route::get('/tickets/{ticket}/attachment', [TicketController::class, 'attachment'])->name('tickets.attachment');

    Route::middleware('can:staff')->group(function () {
        Route::get('/painel', DashboardController::class)->name('dashboard');
        Route::get('/respostas', [CannedReplyController::class, 'index'])->name('canned.index');
        Route::post('/respostas', [CannedReplyController::class, 'store'])->name('canned.store');
        Route::delete('/respostas/{reply}', [CannedReplyController::class, 'destroy'])->name('canned.destroy');
    });

    Route::middleware('can:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    });
});
