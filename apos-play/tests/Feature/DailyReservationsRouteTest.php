<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyReservationsRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_access_daily_reservations(): void
    {
        $user = User::factory()->superadmin()->create();

        $response = $this->actingAs($user)->get('/admin/reservas-del-dia');

        $response->assertStatus(200);
    }

    public function test_owner_can_access_daily_reservations(): void
    {
        $user = User::factory()->owner()->create();

        $response = $this->actingAs($user)->get('/admin/reservas-del-dia');

        $response->assertStatus(200);
    }

    public function test_regular_user_cannot_access_daily_reservations(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/reservas-del-dia');

        $response->assertStatus(403);
    }

    public function test_staff_cannot_access_admin_daily_reservations(): void
    {
        $user = User::factory()->staff()->create();

        $response = $this->actingAs($user)->get('/admin/reservas-del-dia');

        $response->assertStatus(403);
    }
}
