<?php

namespace Database\Seeders;

use App\Enums\LoyaltyPointType;
use App\Enums\ReservationStatus;
use App\Models\LoyaltyPoint;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

class LoyaltyPointSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Generando puntos de fidelidad...');

        $paidReservations = Reservation::where('status', ReservationStatus::CONFIRMED)
            ->orWhere('status', ReservationStatus::PAID)
            ->get();

        $pointsCreated = 0;

        foreach ($paidReservations as $reservation) {
            LoyaltyPoint::create([
                'user_id' => $reservation->user_id,
                'reservation_id' => $reservation->id,
                'points' => config('loyalty.points_per_reservation'),
                'type' => LoyaltyPointType::EARNED,
                'description' => 'Puntos por reserva #' . $reservation->id,
            ]);
            $pointsCreated++;
        }

        $this->command->info("Se crearon {$pointsCreated} registros de puntos de fidelidad.");
    }
}
