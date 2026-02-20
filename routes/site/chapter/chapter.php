<?php

use App\Http\Controllers\Api\ChapterController;
use Illuminate\Support\Facades\Route;

Route::apiResource('chapters', ChapterController::class)->only(['index', 'show']);
