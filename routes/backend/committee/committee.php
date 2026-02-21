<?php

use App\Http\Controllers\Api\CommitteeController;
use Illuminate\Support\Facades\Route;

Route::resource('committees', CommitteeController::class);
Route::post('committees/{committee}', [CommitteeController::class, 'update']);
