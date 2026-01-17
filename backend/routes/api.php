<?php

/**
 * Rotas da API.
 * 
 * Todas as rotas exceto login requerem autenticação via Sanctum.
 */

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Dashboard\DashboardController;
use App\Presentation\Http\Controllers\API\Customer\CustomerController;
use App\Presentation\Http\Controllers\API\Customer\CustomerSegmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::get('/dashboard/metrics', [DashboardController::class, 'metrics']);
    Route::get('/dashboard/recent-activities', [DashboardController::class, 'recentActivities']);

    Route::get('customer-segments', [CustomerSegmentController::class, 'index']);
    Route::apiResource('customers', CustomerController::class);
});
