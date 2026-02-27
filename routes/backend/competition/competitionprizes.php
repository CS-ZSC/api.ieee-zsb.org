<?php

use App\Http\Controllers\Api\CompetitionPrizeController;
use Illuminate\Support\Facades\Route;

Route::get('competitions/{competition}/prizes', [CompetitionPrizeController::class, 'index']);
Route::post('competitions/{competition}/prizes', [CompetitionPrizeController::class, 'store']);
Route::put('competitions/{competition}/prizes/{prize}', [CompetitionPrizeController::class, 'update']);
Route::delete('competitions/{competition}/prizes/{prize}', [CompetitionPrizeController::class, 'destroy']);
