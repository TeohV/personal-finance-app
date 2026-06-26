<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\MonthlyBudget;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MonthlyBudget>
 */
class MonthlyBudgetFactory extends Factory
{
    protected $model = MonthlyBudget::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(['type' => 'expense']),
            'month_year' => now()->startOfMonth()->toDateString(),
            'allocated_amount' => fake()->randomFloat(2, 50, 1000),
        ];
    }
}
