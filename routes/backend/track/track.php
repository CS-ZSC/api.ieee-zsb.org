<?php

use App\Http\Controllers\Api\TrackController;
use Illuminate\Support\Facades\Route;

Route::resource('tracks', TrackController::class);
Route::post('tracks/{track}', [TrackController::class, 'update']);
