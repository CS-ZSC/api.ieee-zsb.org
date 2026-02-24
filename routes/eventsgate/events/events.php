<?php

use App\Http\Controllers\Api\EventController;
use Illuminate\Support\Facades\Route;

// Event CRUD Routes
Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('events', EventController::class);

});
