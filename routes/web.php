<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// Route::middleware('auth')->prefix('api')->group(function () {
Route::prefix('api')->group(function () {

    Route::get('/me', [AccountController::class, 'me']);
    Route::put('/me', [AccountController::class, 'updateAccount']);
    Route::put('/me/password', [AccountController::class, 'changePassword']);
});

Route::get('/{any}', function () {
    return view('app'); // Ứng dụng Vue.js
})->where('any', '.*');
