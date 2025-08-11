<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\ModulesController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\BillersController;
use App\Http\Controllers\CustomersController;

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


// User routes
Route::get('/users', [UsersController::class, 'index']);                    // Get all users
Route::post('/users', [UsersController::class, 'store']);                   // Add user
Route::put('/users/{id}', [UsersController::class, 'update']);              // Edit user
Route::patch('/users/{id}/toggle-status', [UsersController::class, 'toggleStatus']); // Toggle user status
Route::delete('/users/{id}', [UsersController::class, 'destroy']);          // Delete user



Route::get('/billers', [BillersController::class, 'index']);                     // Get all billers
Route::post('/billers', [BillersController::class, 'store']);                   // Onboard new biller
Route::get('/billers/{id}', [BillersController::class, 'show']);                // Get single biller
Route::put('/billers/{id}', [BillersController::class, 'update']);              // Update biller
Route::patch('/billers/{id}/status', [BillersController::class, 'updateStatus']); // Update biller status
Route::delete('/billers/{id}', [BillersController::class, 'destroy']);          // Delete biller

// Customer routes - 5 basic operations
Route::get('/customers', [CustomersController::class, 'index']);                // Get all customers
Route::get('/customers/{id}', [CustomersController::class, 'show']);           // Get single customer
Route::post('/customers', [CustomersController::class, 'store']);              // Add customer
Route::put('/customers/{id}', [CustomersController::class, 'update']);         // Edit customer
Route::delete('/customers/{id}', [CustomersController::class, 'destroy']);     // Delete customer

// Customer bulk import (placeholder for future implementation)
Route::post('/customers/bulk-import', [CustomersController::class, 'bulkImport']); // Bulk import from CSV





