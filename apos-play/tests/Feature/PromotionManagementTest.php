<?php

namespace Tests\Feature;

use App\Enums\PromotionType;
use App\Livewire\Admin\Promotions\Form;
use App\Livewire\Admin\Promotions\Index;
use App\Models\Court;
use App\Models\CourtAddress;
use App\Models\CourtsXAdmin;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PromotionManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        $user = User::factory()->create();

        $address = CourtAddress::create([
            'street' => 'S', 'number' => '1', 'city' => 'C',
            'province' => 'P', 'zip_code' => 'Z', 'country' => 'Co',
        ]);

        $court = Court::create([
            'name' => 'Court', 'price' => 100, 'type' => 'football',
            'court_address_id' => $address->id, 'number_players' => 10,
        ]);

        CourtsXAdmin::create(['court_id' => $court->id, 'user_id' => $user->id]);

        return $user;
    }

    public function test_admin_can_create_promotion(): void
    {
        $admin = $this->createAdmin();

        Livewire::actingAs($admin)
            ->test(Form::class)
            ->set('name', 'Promo Test')
            ->set('type', 'combo')
            ->set('discount_value', '25')
            ->set('starts_at', now()->format('Y-m-d\TH:i'))
            ->set('ends_at', now()->addMonth()->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertRedirect(route('admin.promotions'));

        $this->assertDatabaseHas('promotions', [
            'name' => 'Promo Test',
            'type' => PromotionType::COMBO->value,
            'created_by' => $admin->id,
        ]);
    }

    public function test_overlapping_promotions_are_rejected(): void
    {
        $admin = $this->createAdmin();

        Promotion::create([
            'name' => 'Existing Promo',
            'type' => PromotionType::COMBO,
            'discount_value' => 10,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        Livewire::actingAs($admin)
            ->test(Form::class)
            ->set('name', 'Conflicting Promo')
            ->set('type', 'combo')
            ->set('discount_value', '15')
            ->set('starts_at', now()->addDays(5)->format('Y-m-d\TH:i'))
            ->set('ends_at', now()->addMonths(2)->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertSet('conflictMessage', fn ($v) => str_contains($v, 'Existing Promo'))
            ->assertNoRedirect();

        $this->assertDatabaseMissing('promotions', ['name' => 'Conflicting Promo']);
    }

    public function test_promotion_policy_denies_non_admin(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->can('viewAny', Promotion::class));
        $this->assertFalse($user->can('create', Promotion::class));
    }

    public function test_promotion_policy_allows_admin(): void
    {
        $admin = $this->createAdmin();

        $this->assertTrue($admin->can('viewAny', Promotion::class));
        $this->assertTrue($admin->can('create', Promotion::class));
    }
}
