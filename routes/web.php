<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return file_get_contents(public_path('index.html'));
});
Route::get('login', function () {
    return file_get_contents(public_path('login.html'));
});
Route::get('signup', function () {
    return file_get_contents(public_path('regi.html'));
});
Route::get('logout', function (Illuminate\Http\Request  $request) {
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return view('welcome');
});
