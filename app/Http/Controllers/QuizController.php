<?php

namespace App\Http\Controllers\API;

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

        $user = auth()->user();
        $answers = $request->input('answers');
        $score = 0;
        $totalQuestions = count($answers);

        // Create quiz attempt
        $quizAttempt = QuizAttempt::create([
            'user_id' => $user ? $user->id : null,
            'session_id' => $user ? null : session()->getId(),
            'score' => 0,
            'total_questions' => $totalQuestions,
            'completed_at' => now(),
        ]);

        // Process each answer
        foreach ($answers as $answerData) {
            $question = Question::find($answerData['question_id']);
            $answer = $question->answers()->find($answerData['answer_id']);

            $isCorrect = $answer && $answer->is_correct;
            if ($isCorrect) {
                $score++;
            }

            QuizAnswer::create([
                'quiz_attempt_id' => $quizAttempt->id,
                'question_id' => $answerData['question_id'],
                'answer_id' => $answerData['answer_id'],
                'is_correct' => $isCorrect,
            ]);
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
}
