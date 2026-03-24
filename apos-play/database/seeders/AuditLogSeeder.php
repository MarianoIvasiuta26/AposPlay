<?php

namespace Database\Seeders;

use App\Enums\AuditAction;
use App\Models\AuditLog;
use App\Models\Complex;
use App\Models\Court;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $superadmin = User::where('email', 'admin@aposplay.dev')->first();
        $owner = User::where('email', 'owner@aposplay.dev')->first();
        $client = User::where('email', 'cliente@aposplay.dev')->first();

        if (!$superadmin || !$owner || !$client) {
            return;
        }

        $complex = Complex::first();
        $court = Court::first();
        $reservation = Reservation::first();

        $entries = [
            [
                'user_id' => $superadmin->id,
                'action' => AuditAction::LOGIN->value,
                'auditable_type' => User::class,
                'auditable_id' => $superadmin->id,
                'description' => 'Admin inició sesión',
                'ip_address' => '192.168.1.1',
                'created_at' => now()->subDays(3),
            ],
            [
                'user_id' => $owner->id,
                'action' => AuditAction::LOGIN->value,
                'auditable_type' => User::class,
                'auditable_id' => $owner->id,
                'description' => 'Owner inició sesión',
                'ip_address' => '192.168.1.2',
                'created_at' => now()->subDays(2),
            ],
            [
                'user_id' => $owner->id,
                'action' => AuditAction::CREATED->value,
                'auditable_type' => Complex::class,
                'auditable_id' => $complex?->id ?? 1,
                'description' => 'Owner creó Complejo "' . ($complex?->name ?? 'Test') . '"',
                'new_values' => $complex ? ['name' => $complex->name, 'address' => $complex->address] : null,
                'ip_address' => '192.168.1.2',
                'created_at' => now()->subDays(2)->addHours(1),
            ],
            [
                'user_id' => $owner->id,
                'action' => AuditAction::CREATED->value,
                'auditable_type' => Court::class,
                'auditable_id' => $court?->id ?? 1,
                'description' => 'Owner creó Cancha "' . ($court?->name ?? 'Cancha 1') . '"',
                'new_values' => $court ? ['name' => $court->name, 'price' => $court->price] : null,
                'ip_address' => '192.168.1.2',
                'created_at' => now()->subDays(2)->addHours(2),
            ],
            [
                'user_id' => $client->id,
                'action' => AuditAction::LOGIN->value,
                'auditable_type' => User::class,
                'auditable_id' => $client->id,
                'description' => 'Cliente inició sesión',
                'ip_address' => '192.168.1.10',
                'created_at' => now()->subDay(),
            ],
            [
                'user_id' => $client->id,
                'action' => AuditAction::CREATED->value,
                'auditable_type' => Reservation::class,
                'auditable_id' => $reservation?->id ?? 1,
                'description' => 'Cliente creó Reserva "#' . ($reservation?->id ?? 1) . '"',
                'ip_address' => '192.168.1.10',
                'created_at' => now()->subDay()->addHour(),
            ],
            [
                'user_id' => $client->id,
                'action' => AuditAction::PAYMENT->value,
                'auditable_type' => Reservation::class,
                'auditable_id' => $reservation?->id ?? 1,
                'description' => 'Cliente pagó Reserva "#' . ($reservation?->id ?? 1) . '"',
                'ip_address' => '192.168.1.10',
                'created_at' => now()->subDay()->addHours(2),
            ],
            [
                'user_id' => $owner->id,
                'action' => AuditAction::UPDATED->value,
                'auditable_type' => Court::class,
                'auditable_id' => $court?->id ?? 1,
                'description' => 'Owner editó Cancha "' . ($court?->name ?? 'Cancha 1') . '": precio $5000 → $6000',
                'old_values' => ['price' => 5000],
                'new_values' => ['price' => 6000],
                'ip_address' => '192.168.1.2',
                'created_at' => now()->subHours(6),
            ],
            [
                'user_id' => $superadmin->id,
                'action' => AuditAction::REFUNDED->value,
                'auditable_type' => Reservation::class,
                'auditable_id' => $reservation?->id ?? 1,
                'description' => 'Admin reembolsó Reserva "#' . ($reservation?->id ?? 1) . '"',
                'ip_address' => '192.168.1.1',
                'created_at' => now()->subHours(3),
            ],
            [
                'user_id' => $owner->id,
                'action' => AuditAction::LOGOUT->value,
                'auditable_type' => User::class,
                'auditable_id' => $owner->id,
                'description' => 'Owner cerró sesión',
                'ip_address' => '192.168.1.2',
                'created_at' => now()->subHour(),
            ],
        ];

        foreach ($entries as $entry) {
            AuditLog::create(array_merge($entry, [
                'user_agent' => 'Mozilla/5.0 (Seeder)',
            ]));
        }
    }
}
