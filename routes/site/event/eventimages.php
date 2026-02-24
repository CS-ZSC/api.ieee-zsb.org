<?php

use App\Http\Controllers\Api\EventImageController;
use Illuminate\Support\Facades\Route;

Route::get('events/{slug}/images', [EventImageController::class, 'index']);
