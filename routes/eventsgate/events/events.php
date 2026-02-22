<?php

use App\Http\Controllers\Api\EventController;
use Illuminate\Support\Facades\Route;

// Event CRUD Routes
Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('events', EventController::class);

    Route::post('events/{event}/register', [EventController::class, 'registerUser']);
    Route::delete('events/{event}/unregister', [EventController::class, 'unregisterUser']);

});
