<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement(['Cash', 'Bank Account', 'E-Wallet', 'Savings']),
            'type' => fake()->randomElement(array_keys(Account::types())),
            'opening_balance' => fake()->randomFloat(2, 0, 5000),
            'is_active' => true,
        ];
    }
}
