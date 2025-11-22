<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    // public function __construct()
    // {
    //     // Permite autenticação via JWT (api) ou sessão (web)
    //     // getQuestions não precisa de autenticação
    //     $this->middleware('auth:api,web')->except(['getQuestions', 'submit']);
    // }

    /**
     * Get random active questions for quiz
     */

    public function getQuestions(Request $request)
    {
        // System users (admins) cannot take the quiz
        if (auth()->guard('web')->check()) {
            return response()->json(['error' => 'Administradores não podem realizar o quiz.'], 403);
        }

        // Clients can only take the quiz once
        if (auth()->guard('client')->check()) {
            $user = auth()->guard('client')->user();
            if (QuizAttempt::where('user_id', $user->id)->where('user_type', \App\Models\Client::class)->exists()) {
                return response()->json(['error' => 'Você já realizou o quiz. Apenas uma tentativa é permitida.'], 403);
            }
        }

        $limit = $request->input('limit', 10);

        $questions = Question::with('answers')
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        // Remove the is_correct flag from answers for security
        $questions->each(function ($question) {
            $question->answers->each(function ($answer) {
                unset($answer->is_correct);
            });
        });

        return response()->json($questions);
    }

    /**
     * Submit quiz answers and calculate score
     */
    /**
     * Submit quiz answers and calculate score
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer_id' => 'required|exists:answers,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Detect authenticated user and guard
        $user = null;
        $userType = null;

        // Start session if not started
        if (!$request->hasSession()) {
            $request->setLaravelSession(app('session.store'));
        }

        // Check different guards
        if (auth()->guard('web')->check()) {
            return response()->json(['error' => 'Administradores não podem realizar o quiz.'], 403);
        } elseif (auth()->guard('client')->check()) {
            $user = auth()->guard('client')->user();
            $userType = \App\Models\Client::class;

            // Check if client already took the quiz
            if (QuizAttempt::where('user_id', $user->id)->where('user_type', $userType)->exists()) {
                return response()->json(['error' => 'Você já realizou o quiz. Apenas uma tentativa é permitida.'], 403);
            }

            \Log::info('Quiz submitted by Client', ['id' => $user->id, 'name' => $user->name]);
        } elseif (auth()->guard('parish')->check()) {
            $user = auth()->guard('parish')->user();
            $userType = \App\Models\Parish::class;
            \Log::info('Quiz submitted by Parish', ['id' => $user->id, 'name' => $user->name]);
        } else {
            \Log::info('Quiz submitted anonymously', ['session' => session()->getId()]);
        }

        $answersInput = $request->input('answers');
        $totalQuestions = count($answersInput);

        // Eager load all questions and answers involved in the submission
        $questionIds = array_column($answersInput, 'question_id');
        $questions = Question::with('answers')->whereIn('id', $questionIds)->get()->keyBy('id');

        $score = 0;
        $quizAnswersData = [];

        // Create quiz attempt first
        $quizAttempt = QuizAttempt::create([
            'user_id' => $user ? $user->id : null,
            'user_type' => $userType,
            'session_id' => $user ? null : session()->getId(),
            'score' => 0, // Will update later
            'total_questions' => $totalQuestions,
            'completed_at' => now(),
        ]);

        foreach ($answersInput as $answerData) {
            $questionId = $answerData['question_id'];
            $answerId = $answerData['answer_id'];

            if (!isset($questions[$questionId])) {
                continue; // Should be caught by validation, but safe check
            }

            $question = $questions[$questionId];
            $selectedAnswer = $question->answers->where('id', $answerId)->first();

            $isCorrect = $selectedAnswer && $selectedAnswer->is_correct;
            if ($isCorrect) {
                $score++;
            }

            $quizAnswersData[] = [
                'quiz_attempt_id' => $quizAttempt->id,
                'question_id' => $questionId,
                'answer_id' => $answerId,
                'is_correct' => $isCorrect,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Bulk insert answers
        if (!empty($quizAnswersData)) {
            QuizAnswer::insert($quizAnswersData);
        }

        // Add referral points if user is Client
        if ($userType === \App\Models\Client::class && $user) {
            $score += $user->referral_points;
        }

        // Update quiz attempt with final score
        $quizAttempt->update(['score' => $score]);

        return response()->json([
            'quiz_attempt_id' => $quizAttempt->id,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'percentage' => round(($score / $totalQuestions) * 100, 2),
        ]);
    }

    /**
     * Get quiz results
     */
    public function results($id)
    {
        $quizAttempt = QuizAttempt::with([
            'quizAnswers.question',
            'quizAnswers.answer',
            'quizAnswers.question.answers'
        ])->findOrFail($id);

        // Check if user has access to this quiz attempt
        $user = auth()->user();
        if ($user && $quizAttempt->user_id !== $user->id) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        return response()->json($quizAttempt);
    }

    /**
     * Get user's quiz history
     */
    public function history()
    {
        $user = auth()->user();

        $history = QuizAttempt::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($history);
    }

    /**
     * Validate a single answer and return immediate feedback
     */
    public function validateAnswer(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer_id' => 'required|exists:answers,id'
        ]);

        // System users (admins) cannot take the quiz
        if (auth()->guard('web')->check()) {
            return response()->json(['error' => 'Administradores não podem realizar o quiz.'], 403);
        }

        $question = Question::with('answers')->findOrFail($request->question_id);
        $selectedAnswer = $question->answers()->find($request->answer_id);

        if (!$selectedAnswer) {
            return response()->json(['error' => 'Resposta inválida para esta pergunta'], 400);
        }

        // Security: Do not return the correct answer ID to prevent cheating scripts
        // from easily discovering the correct answer by trial and error without visual feedback.
        // Ideally, for a strict quiz, this endpoint should be removed and validation done only at the end.
        // However, for interactive feedback, we return only boolean correctness.

        return response()->json([
            'correct' => $selectedAnswer->is_correct,
            // 'correct_answer_id' => $correctAnswer ? $correctAnswer->id : null // REMOVED FOR SECURITY
        ]);
    }

    /**
     * Link an anonymous quiz attempt to the authenticated user
     */
    public function linkAttempt(Request $request)
    {
        $request->validate([
            'quiz_attempt_id' => 'required|exists:quiz_attempts,id'
        ]);

        $user = auth()->user();
        $quizAttempt = QuizAttempt::findOrFail($request->quiz_attempt_id);

        // Only link if it doesn't have a user yet
        if (!$quizAttempt->user_id) {
            $quizAttempt->update([
                'user_id' => $user->id,
                'session_id' => null // Clear session ID as it's now owned by user
            ]);
            return response()->json(['message' => 'Quiz vinculado com sucesso!']);
        }

        return response()->json(['message' => 'Este quiz já pertence a um usuário.'], 400);
    }
    /**
     *
     * Get current ranking
     */
    /**
     *
     * Get current ranking
     */
    public function getRanking()
    {
        // Cache ranking for 30 seconds to reduce DB load during high traffic
        $ranking = \Illuminate\Support\Facades\Cache::remember('quiz_ranking', 30, function () {
            return QuizAttempt::select('quiz_attempts.*')
                ->join('clients', function($join) {
                    $join->on('quiz_attempts.user_id', '=', 'clients.id')
                         ->where('quiz_attempts.user_type', '=', \App\Models\Client::class);
                })
                ->orderByDesc('quiz_attempts.score')
                ->orderByDesc('clients.referral_points')
                ->orderBy('quiz_attempts.created_at')
                ->with('user')
                ->take(10)
                ->get()
                ->map(function ($attempt) {
                    return [
                        'name' => $attempt->user ? $attempt->user->name : 'Anônimo',
                        'score' => $attempt->score,
                        'referral_points' => $attempt->user ? $attempt->user->referral_points : 0,
                        'date' => $attempt->created_at->format('d/m/Y H:i'),
                        // Note: is_current_user cannot be cached globally as it depends on the viewer
                        // We will handle this after retrieving from cache
                        'user_id' => $attempt->user_id
                    ];
                });
        });

        // Add user-specific data (is_current_user)
        $currentUserId = auth()->guard('client')->id();
        $rankingWithUserData = $ranking->map(function ($item) use ($currentUserId) {
            $item['is_current_user'] = $currentUserId && $item['user_id'] === $currentUserId;
            unset($item['user_id']); // Remove internal ID if not needed
            return $item;
        });

        return response()->json($rankingWithUserData);
    }
}
