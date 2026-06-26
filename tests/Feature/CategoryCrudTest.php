<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_category()
    {
        $user = User::factory()->create();

        // Simulating a frontend form submission
        $response = $this->actingAs($user)->post('/categories', [
            'name' => 'Freelance Work',
            'type' => 'income',
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', [
            'name' => 'Freelance Work',
            'user_id' => $user->id,
        ]);
    }

    public function test_category_requires_valid_type()
    {
        $user = User::factory()->create();

        // Intentionally submitting a bad 'type' to test validation
        $response = $this->actingAs($user)->post('/categories', [
            'name' => 'Invalid Category',
            'type' => 'not_income_or_expense',
        ]);

        // Assert the session has validation errors for the 'type' field
        $response->assertSessionHasErrors('type');
    }

    public function test_user_can_read_their_categories()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/categories');

        $response->assertStatus(200);
        $response->assertViewHas('categories');
        $response->assertSee($category->name);
    }

    public function test_user_can_update_their_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put('/categories/'.$category->id, [
            'name' => 'Updated Category Name',
            'type' => 'expense',
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category Name',
        ]);
    }

    public function test_user_can_delete_their_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete('/categories/'.$category->id);

        $response->assertRedirect('/categories');
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_category()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $categoryA = Category::factory()->create(['user_id' => $userA->id]);

        $response = $this->actingAs($userB)->delete('/categories/'.$categoryA->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('categories', ['id' => $categoryA->id]);
    }
}
