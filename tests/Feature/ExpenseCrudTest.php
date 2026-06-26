<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_expense()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);

        $response = $this->actingAs($user)->post('/expenses', [
            'category_id' => $category->id,
            'account_id' => $account->id,
            'amount' => 50.00,
            'description' => 'Groceries',
            'date' => '2026-04-23',
        ]);

        $response->assertRedirect('/expenses');
        $this->assertDatabaseHas('expenses', [
            'amount' => 50.00,
            'description' => 'Groceries',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_expense()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $expenseA = Expense::factory()->create(['user_id' => $userA->id]);

        $response = $this->actingAs($userB)->delete('/expenses/'.$expenseA->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('expenses', [
            'id' => $expenseA->id,
        ]);
    }

    // 1. READ Test
    public function test_user_can_read_their_expenses()
    {
        $user = User::factory()->create();
        $expense = Expense::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/expenses');

        $response->assertStatus(200);
        $response->assertViewHas('expenses');
        $response->assertSee($expense->description);
    }

    // 2. UPDATE Test
    public function test_user_can_update_their_expense()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);
        $expense = Expense::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put('/expenses/'.$expense->id, [
            'category_id' => $category->id,
            'account_id' => $account->id,
            'amount' => 99.99,
            'description' => 'Updated Description',
            'date' => '2026-04-24',
        ]);

        $response->assertRedirect('/expenses');

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount' => 99.99,
            'description' => 'Updated Description',
        ]);
    }

    // 3. DELETE Test
    public function test_user_can_delete_their_expense()
    {
        $user = User::factory()->create();
        $expense = Expense::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete('/expenses/'.$expense->id);

        $response->assertRedirect('/expenses');

        $this->assertDatabaseMissing('expenses', [
            'id' => $expense->id,
        ]);
    }

    // 4. VALIDATION EDGE CASE Test
    public function test_expense_requires_valid_amount()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);

        $response = $this->actingAs($user)->post('/expenses', [
            'category_id' => $category->id,
            'account_id' => $account->id,
            'amount' => -10.50,
            'description' => 'Sneaky Expense',
            'date' => '2026-04-23',
        ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_user_cannot_use_another_users_expense_category()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create([
            'user_id' => $otherUser->id,
            'type' => 'expense',
        ]);

        $response = $this->actingAs($user)->post('/expenses', [
            'category_id' => $otherCategory->id,
            'account_id' => $account->id,
            'amount' => 50.00,
            'description' => 'Groceries',
            'date' => '2026-04-23',
        ]);

        $response->assertSessionHasErrors('category_id');
        $this->assertDatabaseMissing('expenses', [
            'user_id' => $user->id,
            'category_id' => $otherCategory->id,
        ]);
    }

    public function test_user_cannot_create_expense_with_archived_account()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create([
            'user_id' => $user->id,
            'is_active' => false,
        ]);
        $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);

        $response = $this->actingAs($user)->post('/expenses', [
            'category_id' => $category->id,
            'account_id' => $account->id,
            'amount' => 50.00,
            'description' => 'Groceries',
            'date' => '2026-04-23',
        ]);

        $response->assertSessionHasErrors('account_id');
        $this->assertDatabaseMissing('expenses', [
            'user_id' => $user->id,
            'account_id' => $account->id,
        ]);
    }
}
