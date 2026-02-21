<?php

use App\Http\Controllers\Api\ChapterController;
use Illuminate\Support\Facades\Route;

Route::resource('chapters', ChapterController::class);
Route::post('chapters/{chapter}', [ChapterController::class, 'update']);
