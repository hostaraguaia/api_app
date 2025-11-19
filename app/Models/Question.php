<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['question', 'difficulty', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function quizAnswers()
    {
        return $this->hasMany(QuizAnswer::class);
    }
}
