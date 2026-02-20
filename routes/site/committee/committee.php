<?php

use App\Http\Controllers\Api\CommitteeController;
use Illuminate\Support\Facades\Route;

Route::apiResource('committees', CommitteeController::class)->only(['index', 'show']);
