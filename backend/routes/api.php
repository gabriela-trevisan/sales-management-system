<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Presentation\Http\Controllers\API\Customer\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::post('/auth/login', [AuthController::class, 'login']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Customers
    Route::apiResource('customers', CustomerController::class);
});
