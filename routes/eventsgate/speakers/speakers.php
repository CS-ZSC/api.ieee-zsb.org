<?php

use App\Http\Controllers\Api\EventSpeakerController;
use Illuminate\Support\Facades\Route;

Route::get('events/{event}/speakers', [EventSpeakerController::class, 'index']);
Route::get('events/{event}/speakers/{speaker}', [EventSpeakerController::class, 'show']);
