<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Livewire\Owner\Staff\Form;
use App\Livewire\Owner\Staff\Index;
use App\Models\Complex;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StaffManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createOwnerWithComplex(): array
    {
        $owner = User::factory()->owner()->create();
        $complex = Complex::create([
            'name' => 'Test Complex',
            'owner_id' => $owner->id,
            'address' => 'Test Address',
        ]);

        return [$owner, $complex];
    }

    public function test_owner_can_view_staff_list(): void
    {
        [$owner, $complex] = $this->createOwnerWithComplex();
        $staff = User::factory()->staff()->create(['name' => 'Staff Member']);
        $complex->staff()->attach($staff->id);

        Livewire::actingAs($owner)
            ->test(Index::class)
            ->assertSee('Staff Member');
    }

    public function test_owner_can_create_staff(): void
    {
        [$owner, $complex] = $this->createOwnerWithComplex();

        Livewire::actingAs($owner)
            ->test(Form::class)
            ->set('name', 'New Staff')
            ->set('email', 'newstaff@test.com')
            ->set('password', 'password123')
            ->set('selectedComplexes', [$complex->id])
            ->call('save')
            ->assertRedirect(route('owner.staff'));

        $this->assertDatabaseHas('users', [
            'name' => 'New Staff',
            'email' => 'newstaff@test.com',
            'role' => UserRole::STAFF->value,
        ]);

        $newStaff = User::where('email', 'newstaff@test.com')->first();
        $this->assertTrue($newStaff->complexesStaff->contains($complex));
    }

    public function test_owner_can_remove_staff_from_complex(): void
    {
        [$owner, $complex] = $this->createOwnerWithComplex();
        $staff = User::factory()->staff()->create();
        $complex->staff()->attach($staff->id);

        Livewire::actingAs($owner)
            ->test(Index::class)
            ->call('removeFromComplex', $staff->id, $complex->id);

        $this->assertFalse($staff->fresh()->complexesStaff->contains($complex));
    }

    public function test_owner_cannot_manage_other_owners_staff(): void
    {
        [$owner1, $complex1] = $this->createOwnerWithComplex();

        $owner2 = User::factory()->owner()->create();
        $complex2 = Complex::create([
            'name' => 'Other Complex',
            'owner_id' => $owner2->id,
        ]);

        $staff = User::factory()->staff()->create(['name' => 'Other Staff']);
        $complex2->staff()->attach($staff->id);

        // Owner1 should not see staff from owner2's complexes
        Livewire::actingAs($owner1)
            ->test(Index::class)
            ->assertDontSee('Other Staff');
    }

    public function test_staff_sees_only_assigned_complex(): void
    {
        [$owner, $complex] = $this->createOwnerWithComplex();
        $staff = User::factory()->staff()->create();
        $complex->staff()->attach($staff->id);

        // Staff can access staff routes
        $this->actingAs($staff)->get(route('staff.reservations'))->assertStatus(200);

        // Staff cannot access owner routes
        $this->actingAs($staff)->get(route('owner.staff'))->assertStatus(403);
    }
}
