<?php

use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

Route::post('teams', [TeamController::class, 'store']);
Route::post('teams/join', [TeamController::class, 'joinWithCode']);
