<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmailVerificationController;

// EventsGate authentication routes
Route::post('/register', [AuthController::class, 'eventsGateRegister']);
Route::post('/verify-registration', [EmailVerificationController::class, 'verifyRegistration']);
Route::post('/send-password-reset-code', [AuthController::class, 'eventsGateSendPasswordResetCode']);
Route::post('/reset-password', [AuthController::class, 'eventsGateResetPassword']);
Route::post('/login', [AuthController::class, 'eventsGateLogin']);
Route::post('/logout', [AuthController::class, 'eventsGateLogout'])->middleware('auth:sanctum');

//load all eventsgate subfolders automatically

foreach (glob(__DIR__ . '/*/*.php') as $routeFile) {
    require $routeFile;
}

