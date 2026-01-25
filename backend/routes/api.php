<?php

/**
 * Rotas da API.
 * 
 * Rate Limiting:
 * - Login: 5 tentativas por minuto (proteção contra brute force)
 * - API autenticada: 60 requisições por minuto por usuário
 * - Geral: 100 requisições por minuto por IP (fallback)
 * 
 * Todas as rotas exceto login requerem autenticação via Sanctum.
 */

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Dashboard\DashboardController;
use App\Presentation\Http\Controllers\API\Customer\CustomerController;
use App\Presentation\Http\Controllers\API\Customer\CustomerSegmentController;
use App\Presentation\Http\Controllers\API\Product\ProductController;
use App\Presentation\Http\Controllers\API\Product\ProductCategoryController;
use App\Presentation\Http\Controllers\API\Proposal\ProposalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Login com rate limiting restrito (5 tentativas/minuto)
Route::post('/auth/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login');

// Rotas autenticadas com rate limiting (60 req/minuto por usuário)
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::get('/dashboard/metrics', [DashboardController::class, 'metrics']);
    Route::get('/dashboard/recent-activities', [DashboardController::class, 'recentActivities']);

    Route::get('customer-segments', [CustomerSegmentController::class, 'index']);
    Route::apiResource('customers', CustomerController::class);
    
    Route::get('product-categories', [ProductCategoryController::class, 'index']);
    Route::apiResource('products', ProductController::class);
    
    Route::apiResource('proposals', ProposalController::class);
    Route::get('proposals/{id}/pdf', [ProposalController::class, 'downloadPdf']);
    Route::post('proposals/{id}/send-email', [ProposalController::class, 'sendEmail']);
});
