<?php

use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('competitions/{competition}/teams', [TeamController::class, 'index']);
Route::get('competitions/{competition}/teams/{team}', [TeamController::class, 'show']);

// Authenticated visitor routes - self service
Route::middleware('auth:sanctum')->group(function () {
    Route::post('competitions/{competition}/teams', [TeamController::class, 'createTeam']);
    Route::post('competitions/{competition}/teams/join', [TeamController::class, 'joinTeam']);
    Route::post('competitions/{competition}/teams/add-member', [TeamController::class, 'addMember']);
    Route::post('competitions/{competition}/teams/remove-member', [TeamController::class, 'removeMember']);
    Route::delete('competitions/{competition}/teams/leave', [TeamController::class, 'leaveTeam']);
});
