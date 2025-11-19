<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Display a listing of the questions.
     */
    public function index()
    {
        $questions = Question::with('answers')->latest()->get();
        return view('user.questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new question.
     */
    public function create()
    {
        return view('user.questions.create');
    }

    /**
     * Store a newly created question.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'difficulty' => 'required|in:easy,medium,hard',
            'is_active' => 'boolean',
            'answers' => 'required|array|min:2|max:4',
            'answers.*.answer' => 'required|string|max:255',
            'answers.*.is_correct' => 'required|boolean',
        ]);

        $question = Question::create([
            'question' => $validated['question'],
            'difficulty' => $validated['difficulty'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        foreach ($validated['answers'] as $answerData) {
            $question->answers()->create($answerData);
        }

        return redirect()->route('user.questions.index')
            ->with('success', 'Pergunta criada com sucesso!');
    }

    /**
     * Display the specified question.
     */
    public function show(Question $question)
    {
        $question->load('answers');
        return view('user.questions.show', compact('question'));
    }

    /**
     * Show the form for editing the question.
     */
    public function edit(Question $question)
    {
        $question->load('answers');
        return view('user.questions.edit', compact('question'));
    }

    /**
     * Update the specified question.
     */
    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'difficulty' => 'required|in:easy,medium,hard',
            'is_active' => 'boolean',
            'answers' => 'required|array|min:2|max:4',
            'answers.*.id' => 'nullable|exists:answers,id',
            'answers.*.answer' => 'required|string|max:255',
            'answers.*.is_correct' => 'required|boolean',
        ]);

        $question->update([
            'question' => $validated['question'],
            'difficulty' => $validated['difficulty'],
            'is_active' => $validated['is_active'] ?? $question->is_active,
        ]);

        // Update existing answers and create new ones
        $answerIds = [];
        foreach ($validated['answers'] as $answerData) {
            if (isset($answerData['id'])) {
                $answer = Answer::find($answerData['id']);
                if ($answer && $answer->question_id === $question->id) {
                    $answer->update([
                        'answer' => $answerData['answer'],
                        'is_correct' => $answerData['is_correct'],
                    ]);
                    $answerIds[] = $answer->id;
                }
            } else {
                $answer = $question->answers()->create([
                    'answer' => $answerData['answer'],
                    'is_correct' => $answerData['is_correct'],
                ]);
                $answerIds[] = $answer->id;
            }
        }

        // Delete removed answers
        $question->answers()->whereNotIn('id', $answerIds)->delete();

        return redirect()->route('user.questions.index')
            ->with('success', 'Pergunta atualizada com sucesso!');
    }

    /**
     * Remove the specified question.
     */
    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('user.questions.index')
            ->with('success', 'Pergunta excluída com sucesso!');
    }

    /**
     * Toggle question active status.
     */
    public function toggleStatus(Question $question)
    {
        $question->update(['is_active' => !$question->is_active]);
        return redirect()->back()
            ->with('success', 'Status da pergunta atualizado!');
    }
}
