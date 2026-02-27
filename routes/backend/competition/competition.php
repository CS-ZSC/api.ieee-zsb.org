<?php

use App\Http\Controllers\Api\CompetitionController;
use Illuminate\Support\Facades\Route;

Route::resource('competitions', CompetitionController::class);
