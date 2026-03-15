<?php

namespace Tests\Feature;

use App\Models\Complex;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplexPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_manage_own_complex(): void
    {
        $owner = User::factory()->owner()->create();
        $complex = Complex::create([
            'name' => 'My Complex',
            'owner_id' => $owner->id,
        ]);

        $this->assertTrue($owner->can('view', $complex));
        $this->assertTrue($owner->can('update', $complex));
        $this->assertTrue($owner->can('delete', $complex));
    }

    public function test_owner_cannot_manage_other_complex(): void
    {
        $owner1 = User::factory()->owner()->create();
        $owner2 = User::factory()->owner()->create();
        $complex = Complex::create([
            'name' => 'Other Complex',
            'owner_id' => $owner2->id,
        ]);

        $this->assertFalse($owner1->can('view', $complex));
        $this->assertFalse($owner1->can('update', $complex));
        $this->assertFalse($owner1->can('delete', $complex));
    }

    public function test_superadmin_can_manage_any_complex(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $owner = User::factory()->owner()->create();
        $complex = Complex::create([
            'name' => 'Any Complex',
            'owner_id' => $owner->id,
        ]);

        $this->assertTrue($superadmin->can('view', $complex));
        $this->assertTrue($superadmin->can('update', $complex));
        $this->assertTrue($superadmin->can('delete', $complex));
    }

    public function test_staff_can_view_assigned_complex(): void
    {
        $owner = User::factory()->owner()->create();
        $staff = User::factory()->staff()->create();
        $complex = Complex::create([
            'name' => 'Staff Complex',
            'owner_id' => $owner->id,
        ]);
        $complex->staff()->attach($staff->id);

        $this->assertTrue($staff->can('view', $complex));
        $this->assertFalse($staff->can('update', $complex));
        $this->assertFalse($staff->can('delete', $complex));
    }

    public function test_staff_cannot_view_unassigned_complex(): void
    {
        $owner = User::factory()->owner()->create();
        $staff = User::factory()->staff()->create();
        $complex = Complex::create([
            'name' => 'Unassigned Complex',
            'owner_id' => $owner->id,
        ]);

        $this->assertFalse($staff->can('view', $complex));
    }

    public function test_regular_user_cannot_manage_complexes(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->can('viewAny', Complex::class));
        $this->assertFalse($user->can('create', Complex::class));
    }
}
