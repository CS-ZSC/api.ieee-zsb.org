<?php

use App\Http\Controllers\Api\DescriptionController;
use Illuminate\Support\Facades\Route;

Route::resource('descriptions', DescriptionController::class);
