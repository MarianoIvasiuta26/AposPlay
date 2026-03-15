<?php

namespace Database\Seeders;

use App\Models\Court;
use App\Models\CourtBlock;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourtBlockSeeder extends Seeder
{
    public function run(): void
    {
        $court = Court::first();
        $admin = User::where('role', 'superadmin')->first();

        if (!$court || !$admin) {
            return;
        }

        CourtBlock::create([
            'court_id' => $court->id,
            'start_date' => now()->addDays(3)->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'start_time' => null,
            'end_time' => null,
            'reason' => 'Mantenimiento programado',
            'created_by' => $admin->id,
        ]);

        CourtBlock::create([
            'court_id' => $court->id,
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'start_time' => '18:00',
            'end_time' => '22:00',
            'reason' => 'Evento privado',
            'created_by' => $admin->id,
        ]);
    }
}
