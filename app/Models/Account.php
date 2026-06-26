<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    public const TYPES = [
        'cash' => 'Cash',
        'bank' => 'Bank',
        'e_wallet' => 'E-Wallet',
        'savings' => 'Savings',
        'investment' => 'Investment',
        'other' => 'Other',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'opening_balance',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public static function types(): array
    {
        return self::TYPES;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function outgoingTransfers()
    {
        return $this->hasMany(Transfer::class, 'from_account_id');
    }

    public function incomingTransfers()
    {
        return $this->hasMany(Transfer::class, 'to_account_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? 'Other';
    }

    public function getBalanceAttribute(): float
    {
        $incomeTotal = (float) $this->incomes()->sum('amount');
        $expenseTotal = (float) $this->expenses()->sum('amount');
        $incomingTotal = (float) $this->incomingTransfers()->sum('amount');
        $outgoingTotal = (float) $this->outgoingTransfers()->sum('amount');

        return (float) $this->opening_balance + $incomeTotal - $expenseTotal + $incomingTotal - $outgoingTotal;
    }

    public function hasActivity(): bool
    {
        return $this->incomes()->exists()
            || $this->expenses()->exists()
            || $this->incomingTransfers()->exists()
            || $this->outgoingTransfers()->exists();
    }
}
