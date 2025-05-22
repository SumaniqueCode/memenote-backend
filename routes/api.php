<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// not logged in user routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('check-reset-token', [AuthController::class, 'checkResetToken']);
    Route::post('change-password', [AuthController::class, 'changePassword']);
});
Route::post('fetch-user', [ProfileController::class, 'fetchUser']);
// loggedin user routes
// Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('login-status', [AuthController::class, 'loginStatus']);
    Route::get('get-user', [ProfileController::class, 'getUser']);
    Route::post('edit-user', [ProfileController::class, 'editUser']);
// });