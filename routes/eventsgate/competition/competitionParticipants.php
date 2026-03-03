<?php

use App\Http\Controllers\Api\CompetitionParticipantController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('competitions/{competition}/participants', [CompetitionParticipantController::class, 'index']);
    Route::post('competitions/{competition}/participants', [CompetitionParticipantController::class, 'store']);
    Route::get('competition-participants/{participant}', [CompetitionParticipantController::class, 'show']);
    Route::delete('competition-participants/{participant}', [CompetitionParticipantController::class, 'destroy']);
});
