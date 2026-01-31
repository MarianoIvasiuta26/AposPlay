<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dias')->insert([
            ['nombre' => 'Lunes'],
            ['nombre' => 'Martes'],
            ['nombre' => 'Miércoles'],
            ['nombre' => 'Jueves'],
            ['nombre' => 'Viernes'],
            ['nombre' => 'Sábado'],
            ['nombre' => 'Domingo'],
        ]);

    }
}
