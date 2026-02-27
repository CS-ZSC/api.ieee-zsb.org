<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Admin Authentication Routes
Route::prefix('auth')->group(function () {
    // Admin Login
    Route::post('/login', [AuthController::class, 'adminLogin']);

    // Admin Logout (requires authentication)
    Route::middleware('auth:sanctum')
        ->post('/logout', [AuthController::class, 'adminLogout']);
});


Route::apiResource('users', UserController::class)
->middleware('auth:sanctum');
Route::post('users/{user}', [UserController::class, 'update'])->middleware('auth:sanctum');
