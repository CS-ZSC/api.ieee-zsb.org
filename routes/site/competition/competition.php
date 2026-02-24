<?php

use App\Http\Controllers\Api\CompetitionController;
use Illuminate\Support\Facades\Route;

Route::apiResource('competitions', CompetitionController::class)->only(['index', 'show']);
