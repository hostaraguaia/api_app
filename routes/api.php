<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::prefix('user')->group(function () {
    Route::post('login', [UserController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        //Route::get('profile', [UserController::class, 'profile']);
    });
});

Route::prefix('client')->group(function () {
    //Route::post('login', [ClientController::class, 'login']);

    Route::middleware('auth:client')->group(function () {
        ///Route::get('profile', [ClientController::class, 'profile']);
    });
});

Route::prefix('purish')->group(function () {
    Route::post('login', [ParoquiaController::class, 'login']);

    Route::middleware('auth:purish')->group(function () {
        //Route::get('profile', [ParoquiaController::class, 'profile']);
    });
});
