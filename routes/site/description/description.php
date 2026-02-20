<?php

use App\Http\Controllers\Api\DescriptionController;
use Illuminate\Support\Facades\Route;

Route::apiResource('descriptions', DescriptionController::class)->only(['index', 'show']);
