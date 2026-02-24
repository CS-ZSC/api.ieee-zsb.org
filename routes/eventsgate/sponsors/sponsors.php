<?php

use App\Http\Controllers\Api\EventSponsorController;
use Illuminate\Support\Facades\Route;

Route::apiResource('sponsors', EventSponsorController::class);