<?php

use App\Http\Controllers\Api\CompetitionParticipantController;
use Illuminate\Support\Facades\Route;

Route::get('competitions/{competition}/participants', [CompetitionParticipantController::class, 'index']);
Route::get('competitions/{competition}/participants/{participant}', [CompetitionParticipantController::class, 'show']);
