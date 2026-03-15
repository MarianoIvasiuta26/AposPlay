<?php

namespace Database\Seeders;

use App\Enums\UserRole;
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
            'role' => UserRole::SUPERADMIN,
        ]);

        User::factory()->create([
            'name' => 'Owner',
            'email' => 'owner@aposplay.dev',
            'password' => bcrypt('Owner@123'),
            'role' => UserRole::OWNER,
        ]);

        User::factory()->create([
            'name' => 'Staff',
            'email' => 'staff@aposplay.dev',
            'password' => bcrypt('Staff@123'),
            'role' => UserRole::STAFF,
        ]);

        User::factory()->create([
            'name' => 'Cliente',
            'email' => 'cliente@aposplay.dev',
            'password' => bcrypt('Cliente@123'),
            'role' => UserRole::USER,
        ]);
    }
}
