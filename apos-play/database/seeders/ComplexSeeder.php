<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Complex;
use App\Models\Court;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComplexSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('email', 'owner@aposplay.dev')->first();
        $staff = User::where('email', 'staff@aposplay.dev')->first();

        if (!$owner) {
            return;
        }

        $complex1 = Complex::create([
            'name' => 'Complejo Apostoles Centro',
            'owner_id' => $owner->id,
            'address' => 'Av. San Martin 1234, Apostoles, Misiones',
        ]);

        $complex2 = Complex::create([
            'name' => 'Complejo Apostoles Norte',
            'owner_id' => $owner->id,
            'address' => 'Ruta 14 Km 5, Apostoles, Misiones',
        ]);

        // Assign staff to first complex
        if ($staff) {
            $complex1->staff()->attach($staff->id);
        }

        // Assign existing courts to complexes (split evenly)
        $courts = Court::all();
        $half = (int) ceil($courts->count() / 2);

        $courts->take($half)->each(function ($court) use ($complex1) {
            $court->update(['complex_id' => $complex1->id]);
        });

        $courts->skip($half)->each(function ($court) use ($complex2) {
            $court->update(['complex_id' => $complex2->id]);
        });
    }
}
