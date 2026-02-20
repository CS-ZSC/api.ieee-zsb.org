<?php

use App\Http\Controllers\Api\ActivityController;
use Illuminate\Support\Facades\Route;

Route::resource('activities', ActivityController::class);
