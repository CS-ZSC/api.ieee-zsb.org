<?php

use App\Http\Controllers\Api\SummaryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('summaries', SummaryController::class)->only(['index', 'show']);
