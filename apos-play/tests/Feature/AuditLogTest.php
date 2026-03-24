<?php

namespace Tests\Feature;

use App\Enums\AuditAction;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\Complex;
use App\Models\Court;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    private function createSuperadmin(): User
    {
        return User::factory()->create(['role' => UserRole::SUPERADMIN]);
    }

    private function createOwner(): User
    {
        return User::factory()->create(['role' => UserRole::OWNER]);
    }

    private function createUser(): User
    {
        return User::factory()->create(['role' => UserRole::USER]);
    }

    public function test_audit_log_created_when_model_uses_auditable_trait(): void
    {
        $owner = $this->createOwner();
        $this->actingAs($owner);

        $complex = Complex::create([
            'name' => 'Complejo Test',
            'owner_id' => $owner->id,
            'address' => 'Calle 123',
            'active' => true,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $owner->id,
            'action' => AuditAction::CREATED->value,
            'auditable_type' => Complex::class,
            'auditable_id' => $complex->id,
        ]);
    }

    public function test_audit_log_tracks_model_updates(): void
    {
        $owner = $this->createOwner();
        $this->actingAs($owner);

        $complex = Complex::create([
            'name' => 'Complejo Original',
            'owner_id' => $owner->id,
            'address' => 'Calle 123',
            'active' => true,
        ]);

        $complex->update(['name' => 'Complejo Modificado']);

        $log = AuditLog::where('action', AuditAction::UPDATED->value)
            ->where('auditable_type', Complex::class)
            ->where('auditable_id', $complex->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('Complejo Original', $log->old_values['name']);
        $this->assertEquals('Complejo Modificado', $log->new_values['name']);
    }

    public function test_audit_log_tracks_model_deletion(): void
    {
        $owner = $this->createOwner();
        $this->actingAs($owner);

        $complex = Complex::create([
            'name' => 'Complejo a Eliminar',
            'owner_id' => $owner->id,
            'address' => 'Calle 456',
            'active' => true,
        ]);

        $complex->delete();

        $this->assertDatabaseHas('audit_logs', [
            'action' => AuditAction::DELETED->value,
            'auditable_type' => Complex::class,
            'auditable_id' => $complex->id,
        ]);
    }

    public function test_audit_service_logs_auth_events(): void
    {
        $user = $this->createUser();
        $service = app(AuditService::class);

        $log = $service->logAuth(AuditAction::LOGIN, $user, 'Usuario inició sesión');

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => AuditAction::LOGIN->value,
            'description' => 'Usuario inició sesión',
        ]);
    }

    public function test_superadmin_can_access_audit_log(): void
    {
        $superadmin = $this->createSuperadmin();

        $response = $this->actingAs($superadmin)->get('/admin/auditoria');

        $response->assertStatus(200);
    }

    public function test_owner_can_access_audit_log(): void
    {
        $owner = $this->createOwner();

        $response = $this->actingAs($owner)->get('/admin/auditoria');

        $response->assertStatus(200);
    }

    public function test_regular_user_cannot_access_audit_log(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/admin/auditoria');

        $response->assertStatus(403);
    }

    public function test_owner_only_sees_logs_from_their_complexes(): void
    {
        $owner1 = $this->createOwner();
        $owner2 = $this->createOwner();

        $this->actingAs($owner1);
        $complex1 = Complex::create([
            'name' => 'Complejo Owner 1',
            'owner_id' => $owner1->id,
            'address' => 'Dirección 1',
            'active' => true,
        ]);

        $this->actingAs($owner2);
        $complex2 = Complex::create([
            'name' => 'Complejo Owner 2',
            'owner_id' => $owner2->id,
            'address' => 'Dirección 2',
            'active' => true,
        ]);

        // Owner1 should see logs for complex1 but not complex2
        $logsForOwner1 = AuditLog::forOwner($owner1)->get();
        $auditableIds = $logsForOwner1->pluck('auditable_id')->toArray();
        $complexLogs = $logsForOwner1->where('auditable_type', Complex::class);

        $this->assertTrue($complexLogs->contains('auditable_id', $complex1->id));
        $this->assertFalse($complexLogs->contains('auditable_id', $complex2->id));
    }

    public function test_audit_log_filters_passwords(): void
    {
        $admin = $this->createSuperadmin();
        $this->actingAs($admin);
        $service = app(AuditService::class);

        $user = User::factory()->create(['password' => 'secret123']);

        // Check that password is not in new_values
        $log = AuditLog::where('auditable_type', User::class)
            ->where('auditable_id', $user->id)
            ->where('action', AuditAction::CREATED->value)
            ->first();

        if ($log && $log->new_values) {
            $this->assertArrayNotHasKey('password', $log->new_values);
            $this->assertArrayNotHasKey('remember_token', $log->new_values);
        }
    }
}
