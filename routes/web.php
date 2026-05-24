<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettlementController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'landing'])->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('groups', GroupController::class)->only(['index', 'store', 'show']);
    Route::post('/groups/{group}/members', [GroupController::class, 'addMember'])->name('groups.members.store');
    Route::delete('/groups/{group}/members/{user}', [GroupController::class, 'removeMember'])->name('groups.members.destroy');
    Route::post('/groups/{group}/expenses', [ExpenseController::class, 'store'])->name('groups.expenses.store');
    Route::post('/groups/{group}/settlements', [SettlementController::class, 'store'])->name('groups.settlements.store');
    Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');

    Route::get('/ajax/groups/{group}/balances', [GroupController::class, 'balances'])->name('ajax.groups.balances');
    Route::get('/ajax/groups/{group}/summary', [GroupController::class, 'summary'])->name('ajax.groups.summary');
});
