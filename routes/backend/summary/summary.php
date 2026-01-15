<?php

use App\Http\Controllers\Api\SummaryController;
use Illuminate\Support\Facades\Route;

Route::resource('summaries', SummaryController::class);
