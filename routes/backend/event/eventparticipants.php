<?php

use App\Http\Controllers\Api\EventParticipantController;
use Illuminate\Support\Facades\Route;

Route::get('events/{slug}/participants', [EventParticipantController::class, 'index']);
Route::post('events/{slug}/participants', [EventParticipantController::class, 'store']);
Route::delete('events/{slug}/participants/{participant}', [EventParticipantController::class, 'destroy']);
