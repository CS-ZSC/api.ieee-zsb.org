<?php

use App\Http\Controllers\Api\PositionController;
use Illuminate\Support\Facades\Route;

Route::resource('positions', PositionController::class);
Route::post('positions/{position}/users', [PositionController::class, 'assignUser']);
Route::delete('positions/{position}/users', [PositionController::class, 'removeUser']);
