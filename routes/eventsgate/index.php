<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'EventsGate API';
});

//load all eventsgate subfolders automatically
foreach (glob(__DIR__ . '/*/*.php') as $routeFile) {
    require $routeFile;
}

