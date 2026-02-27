<?php

use App\Http\Controllers\Api\EventImageController;
use Illuminate\Support\Facades\Route;

Route::get('events/{slug}/images', [EventImageController::class, 'index']);
Route::post('events/{slug}/images', [EventImageController::class, 'store']);
Route::delete('events/{slug}/images/{image}', [EventImageController::class, 'destroy']);
