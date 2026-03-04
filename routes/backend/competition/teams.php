<?php

use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('competitions/{competition}/teams', [TeamController::class, 'index']);
Route::get('competitions/{competition}/teams/{team}', [TeamController::class, 'show']);
Route::post('competitions/{competition}/teams', [TeamController::class, 'adminStore']);
Route::delete('competitions/{competition}/teams/{team}', [TeamController::class, 'adminDestroy']);

Route::get('competitions/{competition}/teams/{team}/members', [TeamController::class, 'memberIndex']);
Route::post('competitions/{competition}/teams/{team}/members', [TeamController::class, 'memberStore']);
Route::delete('competitions/{competition}/teams/{team}/members/{member}', [TeamController::class, 'memberDestroy']);
