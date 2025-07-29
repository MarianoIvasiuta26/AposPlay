<?php

namespace App\Livewire\User;

use App\Models\Court;
use App\Models\Schedule;
use App\Models\SchedulesXCourt;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CourtAvailability extends Component
{
    public $courts;
    public $schedules;
    public $hours_availibility;
    public $date;
    public $dayOfWeek;
    public $availableHours;
    
    public function mount()
    {
        $this->date = now();
        $this->dayOfWeek = $this->date->dayOfWeek;
        $this->loadAvailability();
    }

    public function render()
    {
        return view('livewire.user.court-availability');
    }

    public function loadAvailability(int $minContinuousHours = 2)
    {
        $now = Carbon::now();
        $currentDayOfWeek = $now->dayOfWeek;
        $currentTime = $now;
        
        // Obtener todas las canchas
        $this->courts = DB::table('courts')
            ->select('courts.*')
            ->get();

        // Inicializar el array de horas disponibles por cancha
        $this->hours_availibility = [];
        $courtsWithAvailability = [];

        // Para cada cancha, cargar sus horarios
        foreach ($this->courts as $court) {
            $schedules = $this->loadSchedule($court->id);
            $court->weeklySchedule = $this->organizeSchedulesByDayAndTurn($schedules, $currentDayOfWeek, $currentTime, $minContinuousHours);
            
            // Generar array de horas disponibles para esta cancha
            $hoursArray = $this->generateHoursArray($schedules, $court->id);
            $this->hours_availibility[$court->id] = $hoursArray;
            
            // Verificar si la cancha tiene al menos minContinuousHours horas continuas disponibles
            if ($this->hasMinimumContinuousHours($hoursArray, $minContinuousHours)) {
                $courtsWithAvailability[] = $court;
            }
        }
        
        // Actualizar la lista de canchas para mostrar solo las que tienen disponibilidad suficiente
        // $this->courts = collect($courtsWithAvailability);
        
        // Opcional: Mostrar para depuración
        dd($this->courts, $this->schedules, $this->hours_availibility);
    }

    public function loadSchedule(int $courtId)
    {
        // Consulta SQL con JOIN para obtener los horarios de una cancha específica
        $schedules = DB::table('schedules')
            ->join('schedules_x_courts', 'schedules.id', '=', 'schedules_x_courts.schedule_id')
            ->join('courts', 'courts.id', '=', 'schedules_x_courts.court_id')
            ->select(
                'schedules.*',
                'courts.name as court_name',
                'courts.type as court_type',
                'courts.price as court_price'
            )
            ->where('courts.id', $courtId)
            ->where('schedules.is_available', 0)
            ->orderBy('schedules.day_of_week')
            ->orderBy('schedules.turn')
            ->get();
        
        // Almacenar los horarios en el array de schedules indexado por court_id
        $this->schedules = $schedules;
        
        return $schedules;
    }
    
    /**
     * Genera un array de horas disponibles para una colección de horarios
     * @param mixed $schedules Colección de horarios
     * @param int $courtId ID de la cancha
     * @return array Array de horas disponibles
     */
    private function generateHoursArray($schedules, int $courtId = null)
    {
        $hoursArray = [];
        
        foreach ($schedules as $schedule) {
            // Verificar que el horario tenga start_time y end_time
            if (!isset($schedule->start_time) || !isset($schedule->end_time)) {
                continue;
            }
            
            $start = Carbon::createFromFormat('H:i:s', $schedule->start_time);
            $end = Carbon::createFromFormat('H:i:s', $schedule->end_time);
            
            // Generar horas en intervalos de 1 hora
            $current = $start->copy();
            while ($current < $end) {
                $hourKey = $current->format('H:i');
                
                // Agregar la hora al array con información adicional
                $hoursArray[$hourKey] = [
                    'hour' => $hourKey,
                    'day_of_week' => $schedule->day_of_week,
                    'turn' => $schedule->turn,
                    'is_available' => $schedule->is_available ?? true,
                    'schedule_id' => $schedule->id,
                    'court_id' => $courtId
                ];
                
                $current->addHour();
            }
        }
        
        // Ordenar por hora
        ksort($hoursArray);
        
        return $hoursArray;
    }
    
    /**
     * Verifica si un array de horas tiene al menos un número mínimo de horas continuas disponibles
     */
    private function hasMinimumContinuousHours($hoursArray, $minContinuousHours)
    {
        if (empty($hoursArray)) {
            return false;
        }
        
        // Obtener las horas como array ordenado
        $hours = array_keys($hoursArray);
        sort($hours);
        
        // Si hay menos horas que el mínimo requerido, retornar false
        if (count($hours) < $minContinuousHours) {
            return false;
        }
        
        // Verificar si hay al menos minContinuousHours horas continuas
        $continuousCount = 1;
        $previousHour = null;
        
        foreach ($hours as $hour) {
            if ($previousHour !== null) {
                // Convertir a objetos Carbon para comparar
                $current = Carbon::createFromFormat('H:i', $hour);
                $previous = Carbon::createFromFormat('H:i', $previousHour);
                
                // Si la diferencia es de 1 hora, incrementar el contador
                if ($current->diffInHours($previous) === 1) {
                    $continuousCount++;
                    
                    // Si alcanzamos el mínimo requerido, retornar true
                    if ($continuousCount >= $minContinuousHours) {
                        return true;
                    }
                } else {
                    // Reiniciar el contador si hay un salto
                    $continuousCount = 1;
                }
            }
            
            $previousHour = $hour;
        }
        
        // Verificar si al final alcanzamos el mínimo requerido
        return $continuousCount >= $minContinuousHours;
    }
    
    private function organizeSchedulesByDayAndTurn($schedules, $currentDayOfWeek, $currentTime, $minContinuousHours = 2)
    {
        $weeklySchedule = [];
        $hasAvailability = false;
        
        foreach ($schedules as $schedule) {
            // Inicializar la estructura para este día si no existe
            if (!isset($weeklySchedule[$schedule->day_of_week])) {
                $weeklySchedule[$schedule->day_of_week] = [
                    'morning' => null,
                    'afternoon' => null,
                    'available_hours' => []
                ];
            }
            
            // Asignar el horario al turno correspondiente
            $weeklySchedule[$schedule->day_of_week][$schedule->turn] = $schedule;
            
            // Calcular las horas disponibles
            $start = Carbon::createFromFormat('H:i:s', $schedule->start_time);
            $end = Carbon::createFromFormat('H:i:s', $schedule->end_time);
            
            // Si es el día actual, usar la hora actual como punto de inicio
            if ($schedule->day_of_week == $currentDayOfWeek && $currentTime->format('H:i:s') > $start->format('H:i:s')) {
                $start = Carbon::createFromFormat('H:i:s', $currentTime->format('H:i:s'));
            }
            
            // Si no hay suficientes horas entre start y end, continuar con el siguiente horario
            if ($start->diffInHours($end) < $minContinuousHours) {
                continue;
            }
            
            // Generar horas disponibles
            $availableHours = [];
            $current = $start->copy();
            
            while ($current < $end) {
                $availableHours[] = $current->format('H:i');
                $current->addHour();
                $hasAvailability = true;
            }
            
            // Agregar las horas disponibles al día
            $weeklySchedule[$schedule->day_of_week]['available_hours'] = array_merge(
                $weeklySchedule[$schedule->day_of_week]['available_hours'],
                $availableHours
            );
        }
        
        // Ordenar y eliminar duplicados de las horas disponibles
        foreach ($weeklySchedule as $dayOfWeek => $daySchedule) {
            sort($weeklySchedule[$dayOfWeek]['available_hours']);
            $weeklySchedule[$dayOfWeek]['available_hours'] = array_unique($weeklySchedule[$dayOfWeek]['available_hours']);
        }
        
        return collect($weeklySchedule);
    }
}
