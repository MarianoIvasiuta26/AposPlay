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
use Illuminate\Support\Facades\DB;

class ReservationSeeder extends Seeder
{
    private array $morningHours = ['09:00', '10:00', '11:00', '12:00'];
    private array $afternoonHours = ['14:00', '15:00', '16:00', '17:00', '18:00', '19:00'];
    private array $possibleDurations = [1, 2];
    
    // Probabilidad de que una hora sea reservada (0.0 - 1.0)
    private float $reservationProbability = 0.4;

    public function run(): void
    {
        $this->command->info('Generando reservas aleatorias para agosto 2025...');
        
        // Truncar la tabla de reservas para evitar duplicados
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Reservation::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->error('No hay usuarios registrados para crear reservas');
            return;
        }
        
        $courts = Court::with('schedulesXCourt.schedule')->get();
        if ($courts->isEmpty()) {
            $this->command->error('No hay canchas registradas para crear reservas');
            return;
        }
        
        // Definir el rango de fechas para agosto 2025
        $startDate = Carbon::create(2025, 8, 1);
        $endDate = Carbon::create(2025, 8, 31);
        
        $this->command->info('Creando reservas desde ' . $startDate->format('Y-m-d') . ' hasta ' . $endDate->format('Y-m-d'));
        
        // Crear reservas para cada día del mes
        $currentDate = $startDate->copy();
        $reservationsCreated = 0;
        $processedSchedules = [];
        
        while ($currentDate->lte($endDate)) {
            $dayOfWeek = $currentDate->dayOfWeek;
            
            // Para cada cancha
            foreach ($courts as $court) {
                // Obtener los horarios de esta cancha para el día de la semana actual
                $schedulesForDay = $court->schedulesXCourt
                    ->map(function ($scheduleRelation) {
                        return $scheduleRelation->schedule;
                    })
                    ->filter(function ($schedule) use ($dayOfWeek) {
                        return $schedule->day_of_week == $dayOfWeek;
                    });
                
                if ($schedulesForDay->isEmpty()) {
                    continue; // Esta cancha no tiene horarios para este día
                }
                
                // Para cada horario de la cancha en este día
                foreach ($schedulesForDay as $schedule) {
                    // Clave única para evitar duplicados en la restricción única
                    $uniqueKey = $court->id . '-' . $schedule->id . '-' . $currentDate->format('Y-m-d');
                    
                    // Si ya procesamos este horario para esta fecha y cancha, saltamos
                    if (in_array($uniqueKey, $processedSchedules)) {
                        continue;
                    }
                    
                    // Marcar como procesado
                    $processedSchedules[] = $uniqueKey;
                    
                    // Decidir aleatoriamente si crear una reserva para este horario
                    if (mt_rand(1, 100) <= ($this->reservationProbability * 100)) {
                        // Determinar las horas disponibles según el turno
                        $availableHours = $schedule->turn === TurnType::MORNING->value 
                            ? $this->morningHours 
                            : $this->afternoonHours;
                        
                        // Elegir una hora aleatoria
                        $randomHour = $availableHours[array_rand($availableHours)];
                        $duration = $this->possibleDurations[array_rand($this->possibleDurations)];
                        
                        // Crear la reserva con un usuario aleatorio
                        Reservation::create([
                            'court_id' => $court->id,
                            'user_id' => $users->random()->id,
                            'schedule_id' => $schedule->id,
                            'reservation_date' => $currentDate->format('Y-m-d'),
                            'start_time' => $randomHour,
                            'duration_hours' => $duration,
                            'status' => ReservationStatus::CONFIRMED,
                            'total_price' => $court->price * $duration,
                            'notes' => "Reserva de {$duration} hora(s) en {$currentDate->format('d/m/Y')}"
                        ]);
                        
                        $reservationsCreated++;
                    }
                }
            }
            
            // Avanzar al siguiente día
            $currentDate->addDay();
        }
        
        $this->command->info("Se crearon {$reservationsCreated} reservas aleatorias para el mes de agosto 2025");
    }

    private function getRandomStartTime(string $turn): string
    {
        if ($turn === TurnType::MORNING->value) {
            return $this->morningHours[array_rand($this->morningHours)];
        }
        return $this->afternoonHours[array_rand($this->afternoonHours)];
    }
}
