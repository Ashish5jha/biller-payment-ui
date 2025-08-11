<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\ModulesController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RolesController;

Route::post('/register', [AuthController::class, 'register']);
Route::get('/health', [HealthCheckController::class, 'check']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/change-password', [AuthController::class, 'changePassword']);

// Protected route with Passport
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Module routes - Only 3 functionalities
Route::get('/modules', [ModulesController::class, 'index']);           // Get all modules
Route::post('/modules/add-module', [ModulesController::class, 'store']);          // Add module
Route::put('/modules/{id}', [ModulesController::class, 'update']);     // Edit module
Route::delete('/modules/{id}', [ModulesController::class, 'destroy']);     // Delete module


// Permission routes - Only 3 functionalities
Route::get('/permissions', [PermissionsController::class, 'index']);           // Get all permissions
Route::post('/permissions', [PermissionsController::class, 'store']);          // Add permission
Route::put('/permissions/{id}', [PermissionsController::class, 'update']);     // Edit permission
Route::delete('/permissions/{id}', [PermissionsController::class, 'destroy']); // Delete permission

// Role routes
Route::get('/roles', [RolesController::class, 'index']);           // Get all roles
Route::post('/roles', [RolesController::class, 'store']);          // Add role
Route::put('/roles/{id}', [RolesController::class, 'update']);     // Edit role
Route::delete('/roles/{id}', [RolesController::class, 'destroy']); // Delete role










