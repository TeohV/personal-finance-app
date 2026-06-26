<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Income;
use App\Models\MonthlyBudget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_monthly_allocation_page(): void
    {
        $user = User::factory()->create();
        Category::factory()->create([
            'user_id' => $user->id,
            'type' => 'expense',
            'name' => 'Food',
        ]);

        $response = $this->actingAs($user)->get('/allocate?month=2026-04');

        $response->assertOk();
        $response->assertSee('Food');
    }

    public function test_user_can_save_monthly_budget_allocations(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);

        Income::factory()->create([
            'user_id' => $user->id,
            'amount' => 1000,
            'income_date' => '2026-04-10',
        ]);

        $response = $this->actingAs($user)->post('/allocate/budgets', [
            'month' => '2026-04',
            'allocations' => [
                $category->id => 300,
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('monthly_budgets', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month_year' => '2026-04-01 00:00:00',
            'allocated_amount' => 300,
        ]);
    }

    public function test_user_cannot_allocate_to_another_users_category(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create([
            'user_id' => $otherUser->id,
            'type' => 'expense',
        ]);

        $response = $this->actingAs($user)->post('/allocate/budgets', [
            'month' => '2026-04',
            'allocations' => [
                $otherCategory->id => 100,
            ],
        ]);

        $response->assertSessionHasErrors('allocations');
        $this->assertDatabaseMissing('monthly_budgets', [
            'user_id' => $user->id,
            'category_id' => $otherCategory->id,
        ]);
    }

    public function test_total_allocations_cannot_exceed_available_income(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);

        Income::factory()->create([
            'user_id' => $user->id,
            'amount' => 200,
            'income_date' => '2026-04-10',
        ]);

        $response = $this->actingAs($user)->post('/allocate/budgets', [
            'month' => '2026-04',
            'allocations' => [
                $category->id => 300,
            ],
        ]);

        $response->assertSessionHasErrors('allocations');
        $this->assertDatabaseMissing('monthly_budgets', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'allocated_amount' => 300,
        ]);
    }

    public function test_user_cannot_update_another_users_monthly_budget_directly(): void
    {
        $user = User::factory()->create();
        $otherBudget = MonthlyBudget::factory()->create();

        $response = $this->actingAs($user)->post('/allocate/budgets', [
            'month' => $otherBudget->month_year->format('Y-m'),
            'allocations' => [
                $otherBudget->category_id => 100,
            ],
        ]);

        $response->assertSessionHasErrors('allocations');
    }
}
