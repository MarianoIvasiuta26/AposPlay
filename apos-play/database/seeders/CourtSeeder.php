<?php

namespace Database\Seeders;

use App\Enums\CourtType;
use App\Models\Court;
use App\Models\CourtAddress;
use Illuminate\Database\Seeder;

class CourtSeeder extends Seeder
{
    public function run(): void
    {
        $addresses = CourtAddress::all();

        // Canchas de fútbol
        foreach ($addresses as $address) {
            // Cancha de fútbol 5
            Court::create([
                'name' => "Fútbol 5 - {$address->city}",
                'price' => 15000.00,
                'type' => CourtType::FUTBOL,
                'number_players' => 5,
                'court_address_id' => $address->id,
            ]);

            // Cancha de fútbol 7
            Court::create([
                'name' => "Fútbol 7 - {$address->city}",
                'price' => 20000.00,
                'type' => CourtType::FUTBOL,
                'number_players' => 7,
                'court_address_id' => $address->id,
            ]);
        }

        // Canchas de pádel
        foreach ($addresses as $address) {
            // Dos canchas de pádel por dirección
            for ($i = 1; $i <= 2; $i++) {
                Court::create([
                    'name' => "Pádel {$i} - {$address->city}",
                    'price' => 8000.00,
                    'type' => CourtType::PADEL,
                    'number_players' => 4,
                    'court_address_id' => $address->id,
                ]);
            }
        }
    }
}
