<?php

use App\Http\Controllers\Api\EventSponsorController;
use Illuminate\Support\Facades\Route;

Route::get('events/{slug}/sponsors', [EventSponsorController::class, 'index']);
Route::get('events/{slug}/sponsors/{sponsor}', [EventSponsorController::class, 'show']);
Route::post('events/{slug}/sponsors', [EventSponsorController::class, 'store']);
Route::post('events/{slug}/sponsors/{sponsor}', [EventSponsorController::class, 'update']);
Route::delete('events/{slug}/sponsors/{sponsor}', [EventSponsorController::class, 'destroy']);
