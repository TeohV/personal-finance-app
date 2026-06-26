<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_account(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/accounts', [
            'name' => 'Maybank Savings',
            'type' => 'bank',
            'opening_balance' => 250.00,
        ]);

        $response->assertRedirect('/accounts');
        $this->assertDatabaseHas('accounts', [
            'user_id' => $user->id,
            'name' => 'Maybank Savings',
            'type' => 'bank',
            'opening_balance' => 250.00,
        ]);
    }

    public function test_user_can_view_accounts_page(): void
    {
        $user = User::factory()->create();
        Account::factory()->create([
            'user_id' => $user->id,
            'name' => 'Cash',
            'type' => 'cash',
            'opening_balance' => 100.00,
        ]);

        $response = $this->actingAs($user)->get('/accounts');

        $response->assertStatus(200);
        $response->assertSee('Accounts');
        $response->assertSee('Cash');
        $response->assertSee('Record transfer');
    }

    public function test_bank_withdrawal_is_a_transfer_not_income_or_expense(): void
    {
        $user = User::factory()->create();
        $bank = Account::factory()->create([
            'user_id' => $user->id,
            'name' => 'Bank',
            'type' => 'bank',
            'opening_balance' => 1000.00,
        ]);
        $cash = Account::factory()->create([
            'user_id' => $user->id,
            'name' => 'Cash',
            'type' => 'cash',
            'opening_balance' => 50.00,
        ]);

        $response = $this->actingAs($user)->post('/transfers', [
            'from_account_id' => $bank->id,
            'to_account_id' => $cash->id,
            'amount' => 200.00,
            'transfer_date' => '2026-06-26',
            'description' => 'ATM withdrawal',
        ]);

        $response->assertRedirect('/accounts');
        $this->assertDatabaseHas('transfers', [
            'user_id' => $user->id,
            'from_account_id' => $bank->id,
            'to_account_id' => $cash->id,
            'amount' => 200.00,
        ]);
        $this->assertSame(800.00, $bank->fresh()->balance);
        $this->assertSame(250.00, $cash->fresh()->balance);
        $this->assertSame(0, Income::where('user_id', $user->id)->count());
        $this->assertSame(0, Expense::where('user_id', $user->id)->count());
    }

    public function test_transfer_cannot_use_another_users_account(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $cash = Account::factory()->create(['user_id' => $user->id, 'opening_balance' => 100.00]);
        $otherBank = Account::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->post('/transfers', [
            'from_account_id' => $cash->id,
            'to_account_id' => $otherBank->id,
            'amount' => 20.00,
            'transfer_date' => '2026-06-26',
        ]);

        $response->assertSessionHasErrors('to_account_id');
        $this->assertDatabaseCount('transfers', 0);
    }

    public function test_transfer_cannot_exceed_source_account_balance(): void
    {
        $user = User::factory()->create();
        $bank = Account::factory()->create(['user_id' => $user->id, 'opening_balance' => 50.00]);
        $cash = Account::factory()->create(['user_id' => $user->id, 'opening_balance' => 0.00]);

        $response = $this->actingAs($user)->post('/transfers', [
            'from_account_id' => $bank->id,
            'to_account_id' => $cash->id,
            'amount' => 75.00,
            'transfer_date' => '2026-06-26',
        ]);

        $response->assertSessionHasErrors('amount');
        $this->assertDatabaseCount('transfers', 0);
    }

    public function test_income_and_expense_update_account_balance(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id, 'opening_balance' => 100.00]);
        $incomeCategory = Category::factory()->create(['user_id' => $user->id, 'type' => 'income']);
        $expenseCategory = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);

        Income::factory()->create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => $incomeCategory->id,
            'amount' => 500.00,
        ]);
        Expense::factory()->create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => $expenseCategory->id,
            'amount' => 125.00,
        ]);

        $this->assertSame(475.00, $account->fresh()->balance);
    }
}
