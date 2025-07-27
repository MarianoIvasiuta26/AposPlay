<?php

namespace Database\Seeders;

use App\Models\CourtAddress;
use Illuminate\Database\Seeder;

class CourtAddressSeeder extends Seeder
{
    public function run(): void
    {
        $addresses = [
            [
                'street' => 'Av. Libertador',
                'number' => '1234',
                'city' => 'Buenos Aires',
                'province' => 'Buenos Aires',
                'zip_code' => '1425',
                'country' => 'Argentina',
            ],
            [
                'street' => 'Av. San Martín',
                'number' => '567',
                'city' => 'Córdoba',
                'province' => 'Córdoba',
                'zip_code' => '5000',
                'country' => 'Argentina',
            ],
            [
                'street' => 'Av. Colón',
                'number' => '890',
                'city' => 'Rosario',
                'province' => 'Santa Fe',
                'zip_code' => '2000',
                'country' => 'Argentina',
            ],
        ];

        foreach ($addresses as $address) {
            CourtAddress::create($address);
        }
    }
}
