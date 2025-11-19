<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/quiz', function () {
    return view('quiz.index');
})->name('quiz.start');

// Quiz API Routes (using web middleware for session support)
Route::prefix('api/quiz')->group(function () {
    Route::get('/questions', [App\Http\Controllers\QuizController::class, 'getQuestions']);
    Route::post('/submit', [App\Http\Controllers\QuizController::class, 'submit']);
    Route::post('/validate-answer', [App\Http\Controllers\QuizController::class, 'validateAnswer']);
});

// Client Authentication Routes
Route::prefix('client')->name('client.')->group(function () {
    // Guest routes
    Route::middleware('guest:client')->group(function () {
        Route::get('/login', [App\Http\Controllers\ClientAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [App\Http\Controllers\ClientAuthController::class, 'login'])->name('login.submit');
        Route::get('/register', [App\Http\Controllers\ClientAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [App\Http\Controllers\ClientAuthController::class, 'register'])->name('register.submit');
    });

    // Authenticated routes
    Route::middleware('auth:client')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\ClientAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('/quiz/{id}', [App\Http\Controllers\ClientAuthController::class, 'quizDetails'])->name('quiz.details');
        Route::post('/logout', [App\Http\Controllers\ClientAuthController::class, 'logout'])->name('logout');
    });
});

// Legacy placeholder routes (will be removed later)
Route::get('/login', function () {
    return redirect()->route('client.login');
})->name('login');

Route::get('/register', function () {
    return redirect()->route('client.register');
})->name('register');

Route::post('/logout', function () {
    return redirect()->route('client.logout');
})->name('logout');
