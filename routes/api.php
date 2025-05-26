<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });



// =======================      User Auth Routes     =========================== //
Route::middleware('auth:sanctum')
    ->prefix('auth')
    ->controller(AuthController::class)
    ->group(function () {

        // Register
        Route::post('/register', [AuthController::class, 'register']);

        // Login
        Route::post('/login', [AuthController::class, 'login']);

        // Logout
        Route::post('/logout', [AuthController::class, 'logout']);
    });

// Reset Password Routes
Route::post('/forget-password', [AuthController::class, 'sendOTP']);
Route::post('/verify-otp', [AuthController::class, 'verifyOTP']);
// =======================      End User Auth Routes     =========================== //





// =======================      User Routes     =========================== //
Route::controller(UserController::class)
    ->prefix('users')
    ->group(function () {

        Route::apiResource('users', UserController::class)->except('store');
    });
// =======================      End User Routes     =========================== //
