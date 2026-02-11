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


// EventsGate routes under /api/eventsgate
Route::prefix('eventsgate')->group(function () {
    require __DIR__ . '/eventsgate/index.php';
});
