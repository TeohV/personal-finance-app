<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AllocationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FinancialGoalController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransferController;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsNotBanned;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', EnsureUserIsNotBanned::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::prefix('admin')
        ->name('admin.')
        ->middleware(EnsureUserIsAdmin::class)
        ->group(function () {
            Route::get('/users', [AdminController::class, 'index'])->name('users');
            Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('users.destroy');
            Route::patch('/users/{id}/role', [AdminController::class, 'toggleRole'])->name('toggleRole');
            Route::patch('/users/{id}/ban', [AdminController::class, 'toggleBan'])->name('toggleBan');
            Route::get('/users/{id}/financials', [AdminController::class, 'showFinancials'])->name('user.financials');
        });
    Route::resource('accounts', AccountController::class)->except(['show']);
    Route::post('/transfers', [TransferController::class, 'store'])->name('transfers.store');
    Route::delete('/transfers/{transfer}', [TransferController::class, 'destroy'])->name('transfers.destroy');
    Route::resource('expenses', ExpenseController::class)->except(['show']);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('incomes', IncomeController::class)->except(['show']);
    Route::resource('financial-goals', FinancialGoalController::class)->except(['show']);
    Route::get('/allocate', [AllocationController::class, 'index'])->name('allocations.index');
    Route::post('/allocate/budgets', [AllocationController::class, 'updateBudgets'])->name('allocations.budgets');
    Route::post('/allocate/sweep', [AllocationController::class, 'sweepToGoal'])->name('allocations.sweep');
});

require __DIR__.'/auth.php';
