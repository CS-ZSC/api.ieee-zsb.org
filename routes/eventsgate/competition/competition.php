<?php

use App\Http\Controllers\Api\CompetitionController;
use App\Http\Controllers\Api\CompetitionPrizeController;
use Illuminate\Support\Facades\Route;

Route::apiResource('competitions', CompetitionController::class)->only(['index', 'show']);
Route::get('competitions/{competition}/prizes', [CompetitionPrizeController::class, 'index']);
