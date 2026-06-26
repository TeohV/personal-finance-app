<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncomeCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_income()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'income']);

        $response = $this->actingAs($user)->post('/incomes', [
            'category_id' => $category->id,
            'account_id' => $account->id,
            'amount' => 1500.00,
            'source' => 'Client Project',
            'income_date' => '2026-04-24',
        ]);

        $response->assertRedirect('/incomes');
        $this->assertDatabaseHas('incomes', [
            'amount' => 1500.00,
            'source' => 'Client Project',
        ]);
    }

    public function test_user_can_read_their_incomes()
    {
        $user = User::factory()->create();
        $income = Income::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/incomes');

        $response->assertStatus(200);
        $response->assertViewHas('incomes');
        $response->assertSee($income->source);
    }

    public function test_user_can_update_their_income()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'income']);
        $income = Income::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put('/incomes/'.$income->id, [
            'category_id' => $category->id,
            'account_id' => $account->id,
            'amount' => 5000.00,
            'source' => 'Updated Source',
            'income_date' => '2026-04-25',
        ]);

        $response->assertRedirect('/incomes');
        $this->assertDatabaseHas('incomes', [
            'id' => $income->id,
            'amount' => 5000.00,
            'source' => 'Updated Source',
        ]);
    }

    public function test_user_can_delete_their_income()
    {
        $user = User::factory()->create();
        $income = Income::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete('/incomes/'.$income->id);

        $response->assertRedirect('/incomes');
        $this->assertDatabaseMissing('incomes', [
            'id' => $income->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_income()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $incomeA = Income::factory()->create(['user_id' => $userA->id]);

        $response = $this->actingAs($userB)->delete('/incomes/'.$incomeA->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('incomes', ['id' => $incomeA->id]);
    }

    public function test_income_requires_valid_amount()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'income']);

        $response = $this->actingAs($user)->post('/incomes', [
            'category_id' => $category->id,
            'account_id' => $account->id,
            'amount' => -50.00,
            'source' => 'Bad Income',
            'income_date' => '2026-04-23',
        ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_user_cannot_use_another_users_income_category()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create([
            'user_id' => $otherUser->id,
            'type' => 'income',
        ]);

        $response = $this->actingAs($user)->post('/incomes', [
            'category_id' => $otherCategory->id,
            'account_id' => $account->id,
            'amount' => 1500.00,
            'source' => 'Client Project',
            'income_date' => '2026-04-24',
        ]);

        $response->assertSessionHasErrors('category_id');
        $this->assertDatabaseMissing('incomes', [
            'user_id' => $user->id,
            'category_id' => $otherCategory->id,
        ]);
    }

    public function test_user_cannot_create_income_with_archived_account()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create([
            'user_id' => $user->id,
            'is_active' => false,
        ]);
        $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'income']);

        $response = $this->actingAs($user)->post('/incomes', [
            'category_id' => $category->id,
            'account_id' => $account->id,
            'amount' => 1500.00,
            'source' => 'Client Project',
            'income_date' => '2026-04-24',
        ]);

        $response->assertSessionHasErrors('account_id');
        $this->assertDatabaseMissing('incomes', [
            'user_id' => $user->id,
            'account_id' => $account->id,
        ]);
    }
}
