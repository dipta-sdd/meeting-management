<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\RecurrentSlotController;
use App\Http\Controllers\SlotController;
use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::group(
    [

        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers',
        'prefix' => 'auth'

    ],
    function ($router) {

        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('register', [AuthController::class, 'register']);
        Route::put('change-password', [AuthController::class, 'changePassword']);
        Route::get('/profile', [AuthController::class, 'getProfile']);
        Route::post('/profile/update', [AuthController::class, 'updateProfile']);


        Route::post('/meetings/book', [BookingController::class, 'bookMeeting']);
        Route::get('/meetings', [BookingController::class, 'getMeetings']);
    }
);

Route::group([

    'middleware' => ['api', 'is_host'],
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'slots'

], function ($router) {

    Route::post('create', [SlotController::class, 'create']);
    Route::get('read', [SlotController::class, 'read']);
    Route::get('read/{id}', [SlotController::class, 'read']);
    Route::post('update/{id}', [SlotController::class, 'update']);
    Route::delete('delete/{id}', [SlotController::class, 'destroy']);
});
Route::group([

    'middleware' => ['api', 'is_host'],
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'recurring-slots'

], function ($router) {

    Route::post('create', [RecurrentSlotController::class, 'create']);
    Route::get('read', [RecurrentSlotController::class, 'read']);
    Route::get('read/{id}', [RecurrentSlotController::class, 'read']);
    Route::post('update/{id}', [RecurrentSlotController::class, 'update']);
    Route::delete('delete/{id}', [RecurrentSlotController::class, 'destroy']);
});
Route::group([

    'middleware' => ['api', 'is_host'],
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'guest'

], function ($router) {

    Route::get('search/{name}', [GuestController::class, 'search_name']);
    Route::get('search', [GuestController::class, 'search_name']);
});

