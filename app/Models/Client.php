<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Client extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'phone',
        'address',
        'parish',
        'city',
        'state',
        'zip_code',
        'referral_code',
        'referred_by',
        'referral_points',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all quiz attempts for this client.
     */
    public function quizAttempts()
    {
        return $this->morphMany(QuizAttempt::class, 'user');
    }

    /**
     * Get the client who referred this client.
     */
    public function referrer()
    {
        return $this->belongsTo(Client::class, 'referred_by');
    }

    /**
     * Get the clients referred by this client.
     */
    public function referrals()
    {
        return $this->hasMany(Client::class, 'referred_by');
    }
}
