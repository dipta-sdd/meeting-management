<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('book', function () {
    return file_get_contents(public_path('guest_book_slot.html'));
});
Route::get('search', function () {
    return file_get_contents(public_path('guest_search_host.html'));
});
Route::get('quickslot', function () {
    return file_get_contents(public_path('host_quickslot.html'));
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

// // Route for the host reslot HTML page
// Route::get('/host/reslot', function () {
//     return response()->file(public_path('host_reslot.html'));
// })->middleware('auth'); // Ensure only authenticated users can access this route