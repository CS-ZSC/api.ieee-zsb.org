<?php

use App\Http\Controllers\Api\TrackController;
use Illuminate\Support\Facades\Route;

Route::apiResource('tracks', TrackController::class)->only(['index', 'show']);
