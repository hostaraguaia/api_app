<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClientAuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('client.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('client')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('client.dashboard'));
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form
     */
    public function showRegisterForm(Request $request)
    {
        $referralCode = $request->query('ref');
        return view('client.register', compact('referralCode'));
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
            'password' => 'required|string|min:8|confirmed',
            'cpf' => 'nullable|string|max:14|unique:clients',
            'phone' => 'nullable|string|max:20',
            'parish' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Find referrer if code provided
        $referrer = null;
        if ($request->referral_code) {
            $referrer = Client::where('referral_code', $request->referral_code)->first();
        }

        // Generate unique referral code
        $referralCode = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8));
        while(Client::where('referral_code', $referralCode)->exists()) {
            $referralCode = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8));
        }

        // Create the client
        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'cpf' => $request->cpf,
            'phone' => $request->phone,
            'parish' => $request->parish,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'referral_code' => $referralCode,
            'referred_by' => $referrer ? $referrer->id : null,
        ]);

        // Add points to referrer
        if ($referrer) {
            $referrer->increment('referral_points', 1);
            // Update all quiz attempts scores for the referrer
            $referrer->quizAttempts()->increment('score', 1);
        }

        // Link quiz attempt if provided
        if ($request->quiz_attempt_id) {
            $quizAttempt = QuizAttempt::find($request->quiz_attempt_id);
            if ($quizAttempt && !$quizAttempt->user_id) {
                $quizAttempt->update([
                    'user_id' => $client->id,
                    'user_type' => Client::class,
                    'session_id' => null
                ]);
            }
        }

        // Log the client in
        Auth::guard('client')->login($client);

        return redirect()->route('client.dashboard')
            ->with('success', 'Conta criada com sucesso! Bem-vindo(a)!');
    }

    /**
     * Show the client dashboard
     */
    public function dashboard()
    {
        $client = Auth::guard('client')->user();
        
        // Get quiz attempts
        $attempts = $this->getClientQuizAttempts($client->id);

        // Calculate statistics
        $stats = $this->calculateQuizStatistics($client->id);

        return view('client.dashboard', compact('client', 'attempts', 'stats'));
    }

    /**
     * Get client quiz attempts with pagination
     */
    private function getClientQuizAttempts(int $clientId, int $perPage = 10)
    {
        return QuizAttempt::where('user_id', $clientId)
            ->where('user_type', Client::class)
            ->orderBy('completed_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Calculate quiz statistics for a client
     */
    private function calculateQuizStatistics(int $clientId): array
    {
        $stats = [
            'total_attempts' => QuizAttempt::where('user_id', $clientId)
                ->where('user_type', Client::class)
                ->count(),
            'average_percentage' => 0,
            'best_score' => 0,
            'best_total' => 0,
            'total_score' => 0,
        ];

        // Get client to access referral points
        $client = Client::find($clientId);
        $referralPoints = $client ? $client->referral_points : 0;

        if ($stats['total_attempts'] > 0) {
            $allAttempts = QuizAttempt::where('user_id', $clientId)
                ->where('user_type', Client::class)
                ->get();
            
            // Average percentage
            $totalPercentage = $allAttempts->sum(function($attempt) {
                return ($attempt->score / $attempt->total_questions) * 100;
            });
            $stats['average_percentage'] = $totalPercentage / $stats['total_attempts'];

            // Best score (raw quiz score without referrals, as they are added to the attempt score already)
            // However, to show breakdown, we might want to separate them.
            // But currently attempt->score ALREADY includes referral points.
            
            $bestAttempt = $allAttempts->sortByDesc('score')->first();
            
            if ($bestAttempt) {
                $stats['best_score'] = $bestAttempt->score;
                $stats['best_total'] = $bestAttempt->total_questions;
            }
        }
        
        // Total score is the best attempt score (which already includes referral points)
        // If no attempts, it's just referral points? No, usually you need to do the quiz to be ranked.
        // But let's assume total_score is what is used for ranking.
        $stats['total_score'] = $stats['best_score'];

        return $stats;
    }

    /**
     * Show quiz attempt details
     */
    public function quizDetails($id)
    {
        $client = Auth::guard('client')->user();
        
        $attempt = QuizAttempt::with([
            'quizAnswers.question.answers',
            'quizAnswers.answer'
        ])
        ->where('id', $id)
        ->where('user_id', $client->id)
        ->where('user_type', Client::class)
        ->firstOrFail();

        return view('client.quiz-details', compact('attempt', 'client'));
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
    /**
     * Get client stats for real-time updates
     */
    public function getStats()
    {
        $client = Auth::guard('client')->user();
        $stats = $this->calculateQuizStatistics($client->id);
        
        return response()->json([
            'referral_points' => $client->referral_points,
            'total_score' => $stats['total_score']
        ]);
    }
}
