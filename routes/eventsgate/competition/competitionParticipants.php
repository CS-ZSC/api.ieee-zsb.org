<?php

use App\Http\Controllers\Api\CompetitionParticipantController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('competitions/{competition}/participants', [CompetitionParticipantController::class, 'index']);

// Authenticated visitor routes - self registration
Route::middleware('auth:sanctum')->group(function () {
    Route::post('competitions/{competition}/register', [CompetitionParticipantController::class, 'registerUser']);
    Route::delete('competitions/{competition}/unregister', [CompetitionParticipantController::class, 'unregisterUser']);
});
