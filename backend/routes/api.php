<?php

use App\Http\Controllers\PermissionGui\DashboardController;
use App\Http\Controllers\PermissionGui\PermissionController;
use App\Http\Controllers\PermissionGui\RoleController;
use App\Http\Controllers\PermissionGui\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('permission-gui')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::apiResource('roles', RoleController::class)->except(['create', 'edit']);

    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index']);
        Route::post('/', [PermissionController::class, 'store']);
        Route::post('/bulk', [PermissionController::class, 'bulkStore']);
        Route::delete('/{permission}', [PermissionController::class, 'destroy']);
    });

    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{user}', [UserController::class, 'show']);
    Route::patch('users/{user}', [UserController::class, 'update']);
});
