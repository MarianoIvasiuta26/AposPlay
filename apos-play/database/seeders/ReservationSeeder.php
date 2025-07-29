<?php

namespace Database\Seeders;

use App\Enums\ReservationStatus;
use App\Enums\TurnType;
use App\Models\Court;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    private array $morningHours = ['09:00', '10:00', '11:00', '12:00'];
    private array $afternoonHours = ['14:00', '15:00', '16:00', '17:00', '18:00', '19:00'];
    private array $possibleDurations = [1, 2];

    public function run(): void
    {
        $users = User::all();
        $courts = Court::with('schedulesXCourt.schedule')->get();
        $today = Carbon::now();

        // Crear algunas reservas para la semana actual
        foreach ($courts as $court) {
            foreach ($court->schedulesXCourt as $scheduleRelation) {
                $schedule = $scheduleRelation->schedule;
                
                // Solo crear reservas para algunos horarios (no todos)
                if (rand(0, 1)) {
                    // Calcular la fecha de reserva basada en el día de la semana
                    $reservationDate = $today->copy();
                    $daysUntilReservation = ($schedule->day_of_week - $today->dayOfWeek + 7) % 7;
                    $reservationDate->addDays($daysUntilReservation);

                    // Seleccionar hora de inicio según el turno
                    $startTime = $this->getRandomStartTime($schedule->turn);
                    $duration = $this->possibleDurations[array_rand($this->possibleDurations)];

                    // Crear la reserva con un usuario aleatorio
                    Reservation::create([
                        'court_id' => $court->id,
                        'user_id' => $users->random()->id,
                        'schedule_id' => $schedule->id,
                        'reservation_date' => $reservationDate->format('Y-m-d'),
                        'start_time' => $startTime,
                        'duration_hours' => $duration,
                        'status' => ReservationStatus::CONFIRMED,
                        'total_price' => $court->price * $duration,
                        'notes' => "Reserva de {$duration} hora(s) de prueba"
                    ]);

                    // Marcar el horario como no disponible
                    $schedule->update(['is_available' => false]);
                }
            }
        }
    }

    private function getRandomStartTime(string $turn): string
    {
        if ($turn === TurnType::MORNING->value) {
            return $this->morningHours[array_rand($this->morningHours)];
        }
        return $this->afternoonHours[array_rand($this->afternoonHours)];
    }
}
