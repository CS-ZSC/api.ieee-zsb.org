<?php

use App\Http\Controllers\Api\EventSpeakerController;
use Illuminate\Support\Facades\Route;

Route::apiResource('speakers', EventSpeakerController::class);