<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ACCOUNT_TYPES = [
        'school' => 'School / College',
        'business' => 'Business / Organization',
        'individual' => 'Individual',
    ];

    protected $fillable = [
        'name', 'email', 'password', 'role', 'account_type',
        'organization', 'phone', 'city', 'address',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function quoteRequests(): HasMany
    {
        return $this->hasMany(QuoteRequest::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function accountTypeLabel(): string
    {
        return self::ACCOUNT_TYPES[$this->account_type] ?? ucfirst($this->account_type);
    }

    /** Name shown on quotes: the school/business name when there is one. */
    public function displayName(): string
    {
        return $this->organization ?: $this->name;
    }

    public function initials(): string
    {
        return collect(preg_split('/\s+/', trim($this->displayName())))
            ->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    }
}
