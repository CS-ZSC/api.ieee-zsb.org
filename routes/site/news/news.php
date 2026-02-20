<?php

use App\Http\Controllers\Api\NewsController;
use Illuminate\Support\Facades\Route;

Route::apiResource('news', NewsController::class)->only(['index', 'show']);
