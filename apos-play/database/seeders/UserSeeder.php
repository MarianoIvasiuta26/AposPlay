<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@aposplay.dev',
            'password' => bcrypt('Admin@123'),
        ]);

        User::factory()->create([
            'name' => 'Staff',
            'email' => 'staff@aposplay.dev',
            'password' => bcrypt('Staff@123'),
        ]);

        User::factory()->create([
            'name' => 'Cliente',
            'email' => 'cliente@aposplay.dev',
            'password' => bcrypt('Cliente@123'),
        ]);
    }
}
