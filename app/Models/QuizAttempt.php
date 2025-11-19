<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'session_id',
        'score',
        'total_questions',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Get the owning user model (polymorphic).
     */
    public function user()
    {
        return $this->morphTo();
    }

    public function quizAnswers()
    {
        return $this->hasMany(QuizAnswer::class);
    }
}
