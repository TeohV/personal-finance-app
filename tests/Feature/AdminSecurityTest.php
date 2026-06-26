<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_admin_actions(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $target = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get('/admin/users')
            ->assertForbidden();

        $this->actingAs($user)
            ->patch("/admin/users/{$target->id}/role")
            ->assertForbidden();

        $this->actingAs($user)
            ->patch("/admin/users/{$target->id}/ban")
            ->assertForbidden();

        $this->actingAs($user)
            ->get("/admin/users/{$target->id}/financials")
            ->assertForbidden();
    }

    public function test_banned_user_is_logged_out_from_authenticated_pages(): void
    {
        $user = User::factory()->create(['is_banned' => true]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
