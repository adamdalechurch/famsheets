<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionScheduleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IncomeSourceController;
use App\Http\Controllers\IncomeSourceCategoryController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\BudgetController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);

// all non auth routes should be protected:
Route::middleware('auth:sanctum')->group(function () {
    // dashboard routes
    Route::get('/dashboard-stats', [DashboardController::class, 'index']);
    Route::get('/dashboard-chart', [DashboardController::class, 'chartData']);

    // Transaction Routes
    Route::apiResource('transactions', TransactionController::class);

    //bulk update transactions
    Route::post('/transactions/bulk-update', [TransactionController::class, 'bulkUpdate'])
        ->name('transactions.bulk-update');

    // parse csv as json:
    Route::post('/transactions/parse-csv', [TransactionController::class, 'parse_csv'])
        ->name('transactions.parse-csv');

    // Recurring Transactions (Transaction Schedule)
    Route::apiResource('transaction-schedules', TransactionScheduleController::class);

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Income Sources
    Route::apiResource('income-sources', IncomeSourceController::class);
    Route::apiResource('income-source-categories', IncomeSourceCategoryController::class);

    // User Groups (for shared budgets)
    Route::apiResource('user-groups', UserGroupController::class);

    // Budgets
    Route::apiResource('budgets', BudgetController::class);

    Route::post('/budgets/bulk-update', [BudgetController::class, 'bulkUpdate']);

    Route::get('/user', [AuthController::class, 'user']);

    //

    Route::apiResource('debug', DebugController::class);
});
