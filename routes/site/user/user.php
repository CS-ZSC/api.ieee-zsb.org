<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// All routes are protected by auth:sanctum middleware
Route::middleware('auth:sanctum')->group(function () {
    // Get authenticated user's profile
    Route::get('/profile', [UserController::class, 'profile']);

    // Update user's profile
    Route::put('/update', [UserController::class, 'update']);
});
