<?php

use App\Http\Controllers\Api\EventSpeakerController;
use Illuminate\Support\Facades\Route;

Route::get('events/{slug}/speakers', [EventSpeakerController::class, 'index']);
Route::get('events/{slug}/speakers/{speaker}', [EventSpeakerController::class, 'show']);
