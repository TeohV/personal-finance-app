<?php

namespace Tests\Feature;

use App\Models\FinancialGoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialGoalCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_financial_goal()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/financial-goals', [
            'name' => 'Emergency Fund',
            'description' => 'Save 3 months of expenses',
            'target_amount' => 5000.00,
            'current_amount' => 0.00,
            'target_date' => '2027-01-01',
            'status' => 'in_progress',
        ]);

        $response->assertRedirect('/financial-goals');
        $this->assertDatabaseHas('financial_goals', [
            'user_id' => $user->id,
            'name' => 'Emergency Fund',
            'target_amount' => 5000.00,
        ]);
    }

    public function test_user_can_read_their_financial_goals()
    {
        $user = User::factory()->create();
        $goal = FinancialGoal::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/financial-goals');

        $response->assertStatus(200);
        $response->assertViewHas('goals');
        $response->assertSee($goal->name);
    }

    public function test_user_cannot_edit_another_users_financial_goal()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $goal = FinancialGoal::factory()->create(['user_id' => $userA->id]);

        $response = $this->actingAs($userB)->get('/financial-goals/'.$goal->id.'/edit');

        $response->assertStatus(403);
    }

    public function test_user_can_update_their_financial_goal()
    {
        $user = User::factory()->create();
        $goal = FinancialGoal::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put('/financial-goals/'.$goal->id, [
            'name' => 'Updated Goal Name',
            'description' => 'Updated description',
            'target_amount' => 8000.00,
            'current_amount' => 1000.00,
            'target_date' => '2027-06-01',
            'status' => 'in_progress',
        ]);

        $response->assertRedirect('/financial-goals');
        $this->assertDatabaseHas('financial_goals', [
            'id' => $goal->id,
            'name' => 'Updated Goal Name',
            'current_amount' => 1000.00,
        ]);
    }

    public function test_user_cannot_update_another_users_financial_goal()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $goal = FinancialGoal::factory()->create(['user_id' => $userA->id]);

        $response = $this->actingAs($userB)->put('/financial-goals/'.$goal->id, [
            'name' => 'Hacked Goal',
            'target_amount' => 1.00,
            'current_amount' => 1.00,
            'status' => 'completed',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_financial_goal()
    {
        $user = User::factory()->create();
        $goal = FinancialGoal::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete('/financial-goals/'.$goal->id);

        $response->assertRedirect('/financial-goals');
        $this->assertDatabaseMissing('financial_goals', ['id' => $goal->id]);
    }

    public function test_user_cannot_delete_another_users_financial_goal()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $goal = FinancialGoal::factory()->create(['user_id' => $userA->id]);

        $response = $this->actingAs($userB)->delete('/financial-goals/'.$goal->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('financial_goals', ['id' => $goal->id]);
    }

    public function test_financial_goal_requires_valid_target_amount()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/financial-goals', [
            'name' => 'Bad Goal',
            'target_amount' => -100.00,
            'status' => 'in_progress',
        ]);

        $response->assertSessionHasErrors('target_amount');
    }

    public function test_financial_goal_requires_valid_status()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/financial-goals', [
            'name' => 'Invalid Status Goal',
            'target_amount' => 1000.00,
            'status' => 'unknown_status',
        ]);

        $response->assertSessionHasErrors('status');
    }

    public function test_financial_goal_target_date_must_be_in_the_future()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/financial-goals', [
            'name' => 'Past Goal',
            'target_amount' => 1000.00,
            'current_amount' => 0.00,
            'target_date' => '2020-01-01',
            'status' => 'in_progress',
        ]);

        $response->assertSessionHasErrors('target_date');
    }
}
