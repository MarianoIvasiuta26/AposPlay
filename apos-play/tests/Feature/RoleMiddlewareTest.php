<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_access_admin_routes(): void
    {
        $user = User::factory()->superadmin()->create();

        $response = $this->actingAs($user)->get(route('admin.owners'));

        $response->assertStatus(200);
    }

    public function test_owner_can_access_admin_routes(): void
    {
        $user = User::factory()->owner()->create();

        $response = $this->actingAs($user)->get(route('admin.daily-reservations'));

        $response->assertStatus(200);
    }

    public function test_staff_cannot_access_admin_routes(): void
    {
        $user = User::factory()->staff()->create();

        $response = $this->actingAs($user)->get(route('admin.daily-reservations'));

        $response->assertStatus(403);
    }

    public function test_user_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.daily-reservations'));

        $response->assertStatus(403);
    }

    public function test_deactivated_owner_is_blocked(): void
    {
        $user = User::factory()->owner()->inactive()->create();

        $response = $this->actingAs($user)->get(route('admin.daily-reservations'));

        $response->assertStatus(403);
    }

    public function test_staff_can_access_staff_routes(): void
    {
        $user = User::factory()->staff()->create();

        $response = $this->actingAs($user)->get(route('staff.reservations'));

        $response->assertStatus(200);
    }

    public function test_user_cannot_access_staff_routes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('staff.reservations'));

        $response->assertStatus(403);
    }

    public function test_owner_can_access_owner_routes(): void
    {
        $user = User::factory()->owner()->create();

        $response = $this->actingAs($user)->get(route('owner.complexes'));

        $response->assertStatus(200);
    }

    public function test_user_cannot_access_owner_routes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('owner.complexes'));

        $response->assertStatus(403);
    }
}
