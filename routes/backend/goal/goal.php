<?php

use App\Http\Controllers\Api\GoalController;
use Illuminate\Support\Facades\Route;

Route::resource('goals', GoalController::class);
