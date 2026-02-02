<?php

namespace Database\Seeders;

use App\Enums\CourtType;
use App\Models\Court;
use App\Models\CourtAddress;
use App\Models\CourtsXAdmin;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourtSeeder extends Seeder
{
    public function run(): void
    {
        $addresses = CourtAddress::all();
        $admin = User::where('email', 'admin@aposplay.dev')->first();

        // Si no existe el admin, tomamos el primero o fallamos silenciosamente (aunque debería existir por UserSeeder)
        $userId = $admin ? $admin->id : User::first()->id;

        // Canchas de fútbol
        foreach ($addresses as $address) {
            // Cancha de fútbol 5
            $cancha5 = Court::create([
                'name' => "Fútbol 5 - {$address->city}",
                'price' => 15000.00,
                'type' => CourtType::FUTBOL,
                'number_players' => 5,
                'court_address_id' => $address->id,
            ]);
            
            CourtsXAdmin::create([
                'court_id' => $cancha5->id,
                'user_id' => $userId,
            ]);

            // Cancha de fútbol 7
            $cancha7 = Court::create([
                'name' => "Fútbol 7 - {$address->city}",
                'price' => 20000.00,
                'type' => CourtType::FUTBOL,
                'number_players' => 7,
                'court_address_id' => $address->id,
            ]);

            CourtsXAdmin::create([
                'court_id' => $cancha7->id,
                'user_id' => $userId,
            ]);
        }

        // Canchas de pádel
        foreach ($addresses as $address) {
            // Dos canchas de pádel por dirección
            for ($i = 1; $i <= 2; $i++) {
                $padel = Court::create([
                    'name' => "Pádel {$i} - {$address->city}",
                    'price' => 8000.00,
                    'type' => CourtType::PADEL,
                    'number_players' => 4,
                    'court_address_id' => $address->id,
                ]);

                CourtsXAdmin::create([
                    'court_id' => $padel->id,
                    'user_id' => $userId,
                ]);
            }
        }
    }
}
