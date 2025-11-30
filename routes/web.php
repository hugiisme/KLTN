<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('login'); // Trang đăng nhập
});

Route::get('/{any}', function () {
    return view('app'); // Ứng dụng Vue.js
})->where('any', '.*');
