<?php

use App\Http\Controllers\Api\EventParticipantController;
use Illuminate\Support\Facades\Route;

Route::get('events/{slug}/participants', [EventParticipantController::class, 'index']);
