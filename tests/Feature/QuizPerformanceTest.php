<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Question;
use App\Models\Answer;
use App\Models\QuizAttempt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizPerformanceTest extends TestCase
{
    // use RefreshDatabase; // Be careful with this on existing DBs, maybe use DatabaseTransactions if available or manual cleanup

    public function test_quiz_submission_performance()
    {
        // Create a client
        $client = Client::create([
            'name' => 'Test Client',
            'email' => 'test' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'referral_code' => 'REF' . uniqid(),
        ]);

        // Create questions and answers
        $questions = [];
        for ($i = 0; $i < 10; $i++) {
            $question = Question::create([
                'question' => "Question $i",
                'difficulty' => 'medium', // Added required field if any, assuming default or nullable
                'is_active' => true,
            ]);

            $correctAnswer = Answer::create([
                'question_id' => $question->id,
                'answer' => 'Correct Answer',
                'is_correct' => true,
            ]);

            $wrongAnswer = Answer::create([
                'question_id' => $question->id,
                'answer' => 'Wrong Answer',
                'is_correct' => false,
            ]);

            $questions[] = [
                'question' => $question,
                'correct' => $correctAnswer,
                'wrong' => $wrongAnswer,
            ];
        }

        // Prepare submission data
        $answersData = [];
        foreach ($questions as $q) {
            $answersData[] = [
                'question_id' => $q['question']->id,
                'answer_id' => $q['correct']->id,
            ];
        }

        // Measure time
        $start = microtime(true);

        $response = $this->actingAs($client, 'client')
            ->postJson('/api/quiz/submit', [
                'answers' => $answersData
            ]);

        $end = microtime(true);
        $duration = $end - $start;

        $response->assertStatus(200);
        $response->assertJsonStructure(['quiz_attempt_id', 'score', 'total_questions']);

        // Verify score
        $this->assertEquals(10, $response->json('score')); // 10 correct answers + 0 referral points (initially)

        // Check DB records
        $this->assertDatabaseHas('quiz_attempts', [
            'user_id' => $client->id,
            'score' => 10
        ]);

        $attemptId = $response->json('quiz_attempt_id');
        $this->assertEquals(10, \App\Models\QuizAnswer::where('quiz_attempt_id', $attemptId)->count());

        echo "\nSubmission took: " . round($duration * 1000, 2) . "ms\n";

        // Cleanup
        $client->delete();
        foreach ($questions as $q) {
            $q['question']->answers()->delete();
            $q['question']->delete();
        }
    }
}
