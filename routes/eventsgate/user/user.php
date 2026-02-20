<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

// Apply auth:sanctum middleware to all user routes
Route::middleware(['auth:sanctum'])->group(function () {

    // EventsGate User Profile Routes
    Route::get('/profile', [UserController::class, 'eventsGateProfile']);
    Route::put('/updatemyprofile', [UserController::class, 'eventsGateUpdateProfile']);


});
