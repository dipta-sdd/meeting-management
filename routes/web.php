<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('login', function () {
    return view('login');
});
Route::get('signup', function () {
    return view('signup');
});
Route::get('logout', function (Illuminate\Http\Request  $request) {
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return view('welcome');
});
