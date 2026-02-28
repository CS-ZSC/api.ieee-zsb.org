<?php

use App\Http\Controllers\Api\EventSponsorController;
use Illuminate\Support\Facades\Route;

Route::get('events/{event}/sponsors', [EventSponsorController::class, 'index']);
Route::get('events/{event}/sponsors/{sponsor}', [EventSponsorController::class, 'show']);
