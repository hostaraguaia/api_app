<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ParishController;
use App\Http\Controllers\QuizController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Quiz Routes (with web middleware for session support)
Route::middleware('web')->group(function () {
    Route::get('quiz/questions', [QuizController::class, 'getQuestions']);
    Route::post('quiz/submit', [QuizController::class, 'submit']);
    Route::post('quiz/validate-answer', [QuizController::class, 'validateAnswer']);
});

// User Routes
Route::prefix('user')->group(function () {
    Route::post('login', [UserController::class, 'login']);
    Route::post('register', [UserController::class, 'register']);

    Route::middleware('auth:api')->group(function () {
        Route::get('profile', function (Request $request) {
            return $request->user();
        });
        Route::get('quiz/history', [QuizController::class, 'history']);
        Route::get('quiz/results/{id}', [QuizController::class, 'results']);
        Route::post('quiz/link-attempt', [QuizController::class, 'linkAttempt']);
    });
});

// Client Routes
Route::prefix('client')->group(function () {
    Route::post('login', [ClientController::class, 'login']);
    Route::post('register', [ClientController::class, 'register']);

    Route::middleware('auth:client')->group(function () {
        Route::get('profile', function (Request $request) {
            return $request->user();
        });
    });
});

// Parish Routes
Route::prefix('parish')->group(function () {
    Route::post('login', [ParishController::class, 'login']);
    Route::post('register', [ParishController::class, 'register']);

    Route::middleware('auth:parish')->group(function () {
        Route::get('profile', function (Request $request) {
            return $request->user();
        });
    });
});
