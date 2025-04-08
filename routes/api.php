<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionScheduleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IncomeSourceController;
use App\Http\Controllers\IncomeSourceCategoryController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

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

// Authentication (if using JWT)
Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('register', [App\Http\Controllers\AuthController::class, 'register']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
