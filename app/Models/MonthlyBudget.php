<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'month_year',
        'allocated_amount',
    ];

    protected $casts = [
        'month_year' => 'date',
        'allocated_amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Connects to your teammate's expenses automatically
    public function getSpentAttribute()
    {
        if (! $this->category) {
            return 0;
        }

        $startDate = $this->month_year->copy()->startOfMonth();
        $endDate = $this->month_year->copy()->endOfMonth();

        return Expense::where('category_id', $this->category_id)
            ->where('user_id', $this->user_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
    }

    public function getRemainingAttribute()
    {
        return max(0, $this->allocated_amount - $this->spent);
    }

    public function getUsagePercentageAttribute()
    {
        if ($this->allocated_amount == 0) {
            return 0;
        }

        return min(round(($this->spent / $this->allocated_amount) * 100, 1), 100);
    }

    public function getIsExceededAttribute()
    {
        return $this->spent > $this->allocated_amount;
    }
}
