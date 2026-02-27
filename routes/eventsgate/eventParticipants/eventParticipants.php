<?php

use App\Http\Controllers\Api\EventParticipantController;
use Illuminate\Support\Facades\Route;

// Registering/unregistering for an event
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('events/{event}/register', [EventParticipantController::class, 'registerUser']);
    Route::delete('events/{event}/unregister', [EventParticipantController::class, 'unregisterUser']);

});
