<?php

namespace Tests\Feature;

use App\Models\Complex;
use App\Models\Court;
use App\Models\CourtAddress;
use App\Models\CourtBlock;
use App\Models\User;
use App\Services\CourtBlockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourtBlockTest extends TestCase
{
    use RefreshDatabase;

    private function createCourt(?Complex $complex = null): Court
    {
        $address = CourtAddress::create([
            'street' => 'Test Street', 'number' => '123',
            'city' => 'City', 'province' => 'Prov',
            'zip_code' => '1234', 'country' => 'Country',
        ]);

        return Court::create([
            'name' => 'Test Court', 'price' => 100,
            'type' => 'football', 'court_address_id' => $address->id,
            'number_players' => 10,
            'complex_id' => $complex?->id,
        ]);
    }

    public function test_admin_can_create_court_block(): void
    {
        $admin = User::factory()->superadmin()->create();
        $court = $this->createCourt();

        $this->actingAs($admin);

        $service = new CourtBlockService();
        $block = $service->createBlock([
            'court_id' => $court->id,
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addDays(1)->toDateString(),
            'start_time' => null,
            'end_time' => null,
            'reason' => 'Mantenimiento',
            'created_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('court_blocks', [
            'id' => $block->id,
            'court_id' => $court->id,
            'reason' => 'Mantenimiento',
        ]);
    }

    public function test_blocked_slot_not_shown_as_available(): void
    {
        $court = $this->createCourt();
        $admin = User::factory()->superadmin()->create();

        $date = now()->addDays(2)->toDateString();

        CourtBlock::create([
            'court_id' => $court->id,
            'start_date' => $date,
            'end_date' => $date,
            'start_time' => null,
            'end_time' => null,
            'reason' => 'Full day block',
            'created_by' => $admin->id,
        ]);

        $service = new CourtBlockService();
        $this->assertTrue($service->isSlotBlocked($court->id, $date, '14:00'));
        $this->assertTrue($service->isSlotBlocked($court->id, $date, '10:00'));
    }

    public function test_partial_day_block(): void
    {
        $court = $this->createCourt();
        $admin = User::factory()->superadmin()->create();

        $date = now()->addDays(2)->toDateString();

        CourtBlock::create([
            'court_id' => $court->id,
            'start_date' => $date,
            'end_date' => $date,
            'start_time' => '14:00',
            'end_time' => '18:00',
            'reason' => 'Partial block',
            'created_by' => $admin->id,
        ]);

        $service = new CourtBlockService();
        $this->assertTrue($service->isSlotBlocked($court->id, $date, '14:00'));
        $this->assertTrue($service->isSlotBlocked($court->id, $date, '16:00'));
        $this->assertFalse($service->isSlotBlocked($court->id, $date, '10:00'));
        $this->assertFalse($service->isSlotBlocked($court->id, $date, '18:00'));
    }

    public function test_user_cannot_access_blocks_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/bloqueos');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_blocks_page(): void
    {
        $admin = User::factory()->superadmin()->create();

        $response = $this->actingAs($admin)->get('/admin/bloqueos');

        $response->assertStatus(200);
    }
}
