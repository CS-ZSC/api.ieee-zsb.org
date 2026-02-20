<?php

use App\Http\Controllers\Api\ActivityController;
use Illuminate\Support\Facades\Route;

Route::apiResource('activities', ActivityController::class)->only(['index', 'show']);
