<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionScheduleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IncomeSourceController;
use App\Http\Controllers\IncomeSourceCategoryController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);

// all non auth routes should be protected:
Route::middleware('auth:sanctum')->group(function () {
    // Transaction Routes
    Route::apiResource('transactions', TransactionController::class);

    // Recurring Transactions (Transaction Schedule)
    Route::apiResource('transaction-schedules', TransactionScheduleController::class);

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Income Sources
    Route::apiResource('income-sources', IncomeSourceController::class);
    Route::apiResource('income-source-categories', IncomeSourceCategoryController::class);

    // User Groups (for shared budgets)
    Route::apiResource('user-groups', UserGroupController::class);

    Route::get('/user', [AuthController::class, 'user']);
});
