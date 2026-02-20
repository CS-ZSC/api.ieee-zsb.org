<?php

use Illuminate\Support\Facades\Route;

// Load auth/user routes first (login is public, logout & users have their own auth:sanctum)
require __DIR__ . '/user/user.php';

// All other backend routes require authentication
Route::middleware('auth:sanctum')->group(function () {
    foreach (glob(__DIR__ . '/*/*.php') as $routeFile) {
        if (str_contains($routeFile, '/user/')) {
            continue;
        }
        require $routeFile;
    }
});
