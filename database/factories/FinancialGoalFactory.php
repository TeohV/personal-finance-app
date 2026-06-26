<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialGoalFactory extends Factory
{
    public function definition(): array
    {
        $target = $this->faker->randomFloat(2, 500, 10000);
        $current = $this->faker->randomFloat(2, 0, $target);

        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'target_amount' => $target,
            'current_amount' => $current,
            'target_date' => $this->faker->dateTimeBetween('+1 month', '+2 years')->format('Y-m-d'),
            'status' => 'in_progress',
        ];
    }
}
