<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TokenController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])
            ->middleware('throttle:auth-api');

        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:auth-login');

        Route::middleware(['auth:sanctum', 'throttle:auth-api'])->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    Route::middleware(['auth:sanctum', 'throttle:auth-api'])
        ->prefix('tokens')
        ->group(function () {
            Route::get('/', [TokenController::class, 'index']);
            Route::post('/', [TokenController::class, 'store']);
            Route::delete('/{tokenId}', [TokenController::class, 'destroy']);
        });
});