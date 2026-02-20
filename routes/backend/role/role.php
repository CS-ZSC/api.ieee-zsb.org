<?php

use App\Http\Controllers\Api\RoleController;
use Illuminate\Support\Facades\Route;

Route::resource('roles', RoleController::class);
Route::put('roles/{role}/permissions', [RoleController::class, 'syncPermissions']);
Route::post('roles/{role}/users', [RoleController::class, 'assignUser']);
Route::delete('roles/{role}/users', [RoleController::class, 'removeUser']);
