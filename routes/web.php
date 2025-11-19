<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $ranking = \App\Models\QuizAttempt::with('user')
        ->orderByDesc('score')
        ->orderBy('created_at') // Desempate por quem fez primeiro
        ->take(3)
        ->get();
        
    return view('home', compact('ranking'));
})->name('home');

Route::get('/quiz', function () {
    if (auth()->guard('web')->check()) {
        return redirect()->route('user.dashboard');
    }
    return view('quiz.index');
})->name('quiz.start');

// Quiz API Routes (using web middleware for session support)
Route::prefix('api/quiz')->group(function () {
    Route::get('/questions', [App\Http\Controllers\QuizController::class, 'getQuestions']);
    Route::get('/ranking', [App\Http\Controllers\QuizController::class, 'getRanking']);
    Route::post('/submit', [App\Http\Controllers\QuizController::class, 'submit']);
    Route::post('/validate-answer', [App\Http\Controllers\QuizController::class, 'validateAnswer']);
});

// User (Admin) Routes
Route::prefix('user')->name('user.')->group(function () {
    // Guest routes
    Route::middleware('guest:web')->group(function () {
        Route::get('/login', [App\Http\Controllers\UserAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [App\Http\Controllers\UserAuthController::class, 'login'])->name('login.submit');
        Route::get('/register', [App\Http\Controllers\UserAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [App\Http\Controllers\UserAuthController::class, 'register'])->name('register.submit');
    });

    // Authenticated routes
    Route::middleware('auth:web')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\UserAuthController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [App\Http\Controllers\UserAuthController::class, 'logout'])->name('logout');

        // Question Management Routes
        Route::resource('questions', App\Http\Controllers\QuestionController::class);
        Route::patch('questions/{question}/toggle', [App\Http\Controllers\QuestionController::class, 'toggleStatus'])->name('questions.toggle');

        // Client Management Routes
        Route::get('/clients', [App\Http\Controllers\Admin\ClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/{client}', [App\Http\Controllers\Admin\ClientController::class, 'show'])->name('clients.show');
        Route::get('/quiz-attempts/{id}', [App\Http\Controllers\Admin\ClientController::class, 'quizDetails'])->name('quiz.details');
    });
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
