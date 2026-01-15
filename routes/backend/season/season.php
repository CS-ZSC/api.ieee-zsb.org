<?php

use App\Http\Controllers\Api\SeasonController;
use Illuminate\Support\Facades\Route;

Route::resource('seasons', SeasonController::class);
