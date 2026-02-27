<?php

use App\Http\Controllers\Api\EventController;
use Illuminate\Support\Facades\Route;

Route::resource('events', EventController::class);
Route::post('events/{slug}', [EventController::class, 'update']);
