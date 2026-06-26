<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_banned',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_banned' => 'boolean',
        ];
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function budgets()
    {
        return $this->hasMany(MonthlyBudget::class);
    }

    public function financialGoals()
    {
        return $this->hasMany(FinancialGoal::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->isAdmin();
    }

    public function isBanned(): bool
    {
        return (bool) $this->is_banned;
    }
}
