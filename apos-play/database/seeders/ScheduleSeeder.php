<?php

namespace Database\Seeders;

use App\Enums\DayOfWeekTurns;
use App\Enums\TurnType;
use App\Models\Court;
use App\Models\Schedule;
use App\Models\SchedulesXCourt;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Crear horarios base
        $morningSchedules = $this->createBasicSchedules(TurnType::MORNING, '09:00', '13:00');
        $afternoonSchedules = $this->createBasicSchedules(TurnType::AFTERNOON, '14:00', '20:00');

        // Asignar horarios a cada cancha
        $courts = Court::all();
        foreach ($courts as $court) {
            foreach ($morningSchedules as $schedule) {
                SchedulesXCourt::create([
                    'court_id' => $court->id,
                    'schedule_id' => $schedule->id
                ]);
            }

            foreach ($afternoonSchedules as $schedule) {
                SchedulesXCourt::create([
                    'court_id' => $court->id,
                    'schedule_id' => $schedule->id
                ]);
            }
        }
    }

    private function createBasicSchedules(TurnType $turn, string $startTime, string $endTime): array
    {
        $schedules = [];
        
        // Crear horarios para cada día de la semana
        foreach (DayOfWeekTurns::cases() as $day) {
            $schedules[] = Schedule::create([
                'day_of_week' => $day->value,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'turn' => $turn->value,
                'is_available' => true
            ]);
        }

        return $schedules;
    }
}
