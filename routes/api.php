<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public Authentication
    Route::post('/auth/token', [AuthController::class, 'token']);

    // Authenticated API Endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'me']);
        Route::post('/auth/revoke', [AuthController::class, 'revoke']);
        Route::get('/dashboard/stats', [DashboardApiController::class, 'stats']);
    });
});
