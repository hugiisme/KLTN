<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/login', function () {
    return view('login'); // Trang đăng nhập
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/{any}', function () {
    return view('app'); // Ứng dụng Vue.js
})->where('any', '.*');
