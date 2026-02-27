<?php

use App\Http\Controllers\Api\CompetitionPrizeController;
use Illuminate\Support\Facades\Route;

Route::get('competitions/{competition}/prizes', [CompetitionPrizeController::class, 'index']);
