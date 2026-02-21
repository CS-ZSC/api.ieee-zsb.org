<?php

use App\Http\Controllers\Api\CompetitionController;
use App\Http\Controllers\Api\CompetitionPrizeController;
use Illuminate\Support\Facades\Route;

Route::apiResource('competitions', CompetitionController::class);
Route::apiResource('competitions.prizes', CompetitionPrizeController::class)->shallow();
