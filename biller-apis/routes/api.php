<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HealthCheckController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// These routes do NOT require authentication
Route::post('/register', [AuthController::class, 'register']);
Route::get('/health', [HealthCheckController::class, 'check']);

// This route requires authentication
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});