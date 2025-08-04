<?php



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HealthCheckController;

Route::post('/register', [AuthController::class, 'register']);
Route::get('/health', [HealthCheckController::class, 'check']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/change-password', [AuthController::class, 'changePassword']);
// Protected route with Passport
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
