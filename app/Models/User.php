<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
     * Get all loans for this user
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Get active loans
     */
    public function activeLoans()
    {
        return $this->hasMany(Loan::class)->where('status', 'active');
    }

    /**
     * Get all holds for this user
     */
    public function holds()
    {
        return $this->hasMany(Hold::class);
    }

    /**
     * Get all fines for this user
     */
    public function fines()
    {
        return $this->hasMany(Fine::class);
    }

    /**
     * Get pending fines
     */
    public function pendingFines()
    {
        return $this->hasMany(Fine::class)->where('status', 'pending');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is librarian
     */
    public function isLibrarian(): bool
    {
        return $this->role === 'librarian' || $this->role === 'admin';
    }

    /**
     * Check if user is patron
     */
    public function isPatron(): bool
    {
        return $this->role === 'patron';
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
