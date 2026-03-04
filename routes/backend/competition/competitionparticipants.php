<?php

use App\Http\Controllers\Api\CompetitionParticipantController;
use Illuminate\Support\Facades\Route;

Route::get('competitions/{competition}/participants', [CompetitionParticipantController::class, 'index']);
Route::get('competitions/{competition}/participants/{participant}', [CompetitionParticipantController::class, 'show']);
Route::post('competitions/{competition}/participants', [CompetitionParticipantController::class, 'store']);
Route::delete('competitions/{competition}/participants/{participant}', [CompetitionParticipantController::class, 'destroy']);
