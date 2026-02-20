<?php

use App\Http\Controllers\Api\GoalController;
use Illuminate\Support\Facades\Route;

Route::apiResource('goals', GoalController::class)->only(['index', 'show']);
