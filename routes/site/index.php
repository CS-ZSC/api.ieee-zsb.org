
<?php
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Load all front subfolders automatically
foreach (glob(__DIR__ . '/*/*.php') as $routeFile) {
    require $routeFile;
}


