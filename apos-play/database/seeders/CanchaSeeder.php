<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CanchaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\User::where('email', 'admin@aposplay.dev')->first();

        if ($admin) {
            \App\Models\Cancha::factory(5)->create([
                'user_id' => $admin->id
            ]);
        }

        // Create a few random ones too
        \App\Models\Cancha::factory(3)->create();
    }
}
