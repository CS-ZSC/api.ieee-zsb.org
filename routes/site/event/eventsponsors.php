<?php

use App\Http\Controllers\Api\EventSponsorController;
use Illuminate\Support\Facades\Route;

Route::get('events/{slug}/sponsors', [EventSponsorController::class, 'index']);
Route::get('events/{slug}/sponsors/{sponsor}', [EventSponsorController::class, 'show']);
