<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the clients.
     */
    public function index()
    {
        $clients = Client::withCount('quizAttempts')
            ->latest()
            ->get();

        return view('user.clients.index', compact('clients'));
    }

    /**
     * Display the specified client.
     */
    public function show(Client $client)
    {
        $client->load(['quizAttempts' => function($query) {
            $query->latest();
        }]);
        
        return view('user.clients.show', compact('client'));
    }

    /**
     * Show quiz attempt details for admin
     */
    public function quizDetails($id)
    {
        $attempt = \App\Models\QuizAttempt::with([
            'quizAnswers.question.answers',
            'quizAnswers.answer',
            'user' // Load the user (client) relationship
        ])->findOrFail($id);

        return view('user.clients.quiz-details', compact('attempt'));
    }
}
