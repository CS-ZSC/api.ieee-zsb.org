<?php

use Illuminate\Support\Facades\Route;

// Backend routes under /api/dashboard
Route::prefix('dashboard')->group(function () {
    require __DIR__ . '/backend/index.php';
});

// Frontend routes under /api/site
Route::prefix('site')->group(function () {
    require __DIR__ . '/site/index.php';
});

