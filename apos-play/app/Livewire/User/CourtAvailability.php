<?php

namespace App\Livewire\User;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CourtAvailability extends Component
{
    public $courts;
    public $schedules;
    public $available_hours_with_dates; // Array de horas disponibles con sus fechas correspondientes
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

        // Inicializar el array de horas disponibles con fechas
        $this->available_hours_with_dates = [];
        $courtsWithAvailability = [];

        // Para cada cancha, cargar sus horarios
        foreach ($this->courts as $court) {
            $schedules = $this->loadSchedule($court->id);
            $court->weeklySchedule = $this->organizeSchedulesByDayAndTurn($schedules, $currentDayOfWeek, $currentTime, $minContinuousHours);
            
            // Generar horas no reservadas
            $nonReservedHours = $this->generateNonReservedHoursArray($schedules, $court->id);
            
            // Generar y guardar el array de horas disponibles con fechas
            $this->available_hours_with_dates[$court->id] = $this->generateAvailableHoursWithDates($nonReservedHours, $court->id);
            
            // Verificar si la cancha tiene al menos el mínimo de horas continuas disponibles
            if ($this->hasMinimumContinuousHours($nonReservedHours, $minContinuousHours)) {
                $courtsWithAvailability[] = $court;
            }
        }
        
        // Actualizar la lista de canchas para mostrar solo las que tienen disponibilidad
        $this->courts = collect($courtsWithAvailability);
        
        // Mostrar solo el array de horas disponibles con fechas para depuración
        // dump("Available Hours With Dates:");
        // dump($this->available_hours_with_dates);
    }

    public function loadSchedule(int $courtId)
    {
        // Consulta SQL para obtener los horarios directamente
        $scheduleIds = DB::table('schedules_x_courts')
            ->where('court_id', $courtId)
            ->pluck('schedule_id');
        
        $schedules = DB::table('schedules')
            ->whereIn('id', $scheduleIds)
            ->where('is_available', 0) // Usando 0 ya que parece funcionar con este valor
            ->orderBy('day_of_week')
            ->orderBy('turn')
            ->get();
        
        // Almacenar los horarios en el array de schedules indexado por court_id
        $this->schedules[$courtId] = $schedules;
        
        return $schedules;
    }
    
    /**
     * Genera un array con todas las horas disponibles para una colección de horarios
     * @param mixed $schedules Colección de horarios
     * @param int $courtId ID de la cancha
     * @return array Array de horas disponibles
     */
    private function generateHoursArray($schedules, int $courtId)
    {
        $hoursArray = [];
        
        foreach ($schedules as $schedule) {
            // Verificar si el horario está disponible
            if ($schedule->is_available == 0) { // 0 = disponible, 1 = no disponible
                // Obtener el día de la semana y el turno
                $dayOfWeek = $schedule->day_of_week;
                $turn = $schedule->turn;
                
                // Obtener las horas de inicio y fin del horario
                $startHour = Carbon::parse($schedule->start_time)->format('H:i');
                $endHour = Carbon::parse($schedule->end_time)->format('H:i');
                
                // Generar las horas intermedias en intervalos de 1 hora
                $currentHour = Carbon::parse($startHour);
                $endHourCarbon = Carbon::parse($endHour);
                
                while ($currentHour < $endHourCarbon) {
                    $hourKey = $currentHour->format('H:i');
                    
                    // Agregar la hora al array si no existe
                    if (!isset($hoursArray[$hourKey])) {
                        $hoursArray[$hourKey] = [
                            'hour' => $hourKey,
                            'day_of_week' => $dayOfWeek,
                            'turn' => $turn,
                            'schedule_id' => $schedule->id,
                            'court_id' => $courtId,
                        ];
                    }
                    
                    // Avanzar 1 hora
                    $currentHour->addHour();
                }
            }
        }
        
        return $hoursArray;
    }
    
    /**
     * Genera un array con las horas NO reservadas para una colección de horarios
     * @param mixed $schedules Colección de horarios
     * @param int $courtId ID de la cancha
     * @return array Array de horas NO reservadas
     */
    private function generateNonReservedHoursArray($schedules, int $courtId)
    {
        $nonReservedHours = [];
        $now = Carbon::now();
        $currentDate = $now->toDateString();
        
        // Obtener las reservas existentes para esta cancha
        $reservations = $this->getReservationsForCourt($courtId, $currentDate);
        
        // Generar el array de todas las horas disponibles según los horarios
        $allHours = $this->generateHoursArray($schedules, $courtId);
        
        // Filtrar las horas que no están reservadas
        foreach ($allHours as $hourKey => $hourData) {
            $isReserved = $this->isHourReserved($hourKey, $hourData['day_of_week'], $courtId, $reservations);
            
            if (!$isReserved) {
                $nonReservedHours[$hourKey] = $hourData;
            }
        }
        
        return $nonReservedHours;
    }
    
    /**
     * Genera un array de horas disponibles con sus fechas correspondientes para los próximos 7 días
     * @param array $nonReservedHours Array de horas no reservadas
     * @param int $courtId ID de la cancha
     * @return array Array de horas disponibles con fechas
     */
    private function generateAvailableHoursWithDates(array $nonReservedHours, int $courtId)
    {
        $availableHoursWithDates = [];
        $now = Carbon::now();
        $currentDayOfWeek = $now->dayOfWeek;
        
        // Agrupar las horas por día de la semana
        $hoursByDay = [];
        foreach ($nonReservedHours as $hourKey => $hourData) {
            $dayOfWeek = $hourData['day_of_week'];
            if (!isset($hoursByDay[$dayOfWeek])) {
                $hoursByDay[$dayOfWeek] = [];
            }
            $hoursByDay[$dayOfWeek][$hourKey] = $hourData;
        }
        
        // Generar disponibilidad para los próximos 7 días
        for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
            // Calcular la fecha para este día
            $targetDate = $now->copy()->addDays($dayOffset);
            $targetDayOfWeek = $targetDate->dayOfWeek;
            $dateStr = $targetDate->toDateString();
            
            // Si tenemos horarios para este día de la semana
            if (isset($hoursByDay[$targetDayOfWeek])) {
                $hours = $hoursByDay[$targetDayOfWeek];
                
                // Para el día actual, filtrar las horas que ya pasaron
                if ($dayOffset === 0) {
                    $currentHour = $now->format('H:i');
                    $hours = array_filter($hours, function($hourData, $hourKey) use ($currentHour) {
                        return $hourKey >= $currentHour;
                    }, ARRAY_FILTER_USE_BOTH);
                }
                
                // Agregar las horas disponibles para este día
                foreach ($hours as $hourKey => $hourData) {
                    try {
                        $availableHoursWithDates[] = [
                            'court_id' => $courtId,
                            'hour' => $hourKey,
                            'date' => $dateStr,
                            'day_of_week' => $targetDayOfWeek,
                            'day_name' => $this->getDayName($targetDayOfWeek),
                            'turn' => $hourData['turn'] ?? 'morning', // Valor por defecto si no existe
                            'schedule_id' => $hourData['schedule_id'] ?? 0 // Valor por defecto si no existe
                        ];
                    } catch (\Exception $e) {
                        // Registrar el error pero continuar con el siguiente
                        \Illuminate\Support\Facades\Log::error('Error al procesar hora disponible: ' . $e->getMessage());
                        \Illuminate\Support\Facades\Log::error('Datos de la hora: ' . json_encode($hourData));
                    }
                }
            }
        }
        
        // Ordenar por fecha y hora
        usort($availableHoursWithDates, function($a, $b) {
            if ($a['date'] === $b['date']) {
                return $a['hour'] <=> $b['hour'];
            }
            return $a['date'] <=> $b['date'];
        });
        
        return $availableHoursWithDates;
    }
    
    /**
     * Obtiene el nombre del día de la semana
     * @param int $dayOfWeek Número del día de la semana (0-6, donde 0 es domingo)
     * @return string Nombre del día de la semana
     */
    private function getDayName(int $dayOfWeek)
    {
        $days = [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];
        
        return $days[$dayOfWeek] ?? 'Desconocido';
    }
    
    /**
     * Obtiene las reservas para una cancha específica a partir de una fecha dada
     * @param int $courtId ID de la cancha
     * @param string $fromDate Fecha a partir de la cual obtener las reservas (formato Y-m-d)
     * @return array Array de reservas
     */
    private function getReservationsForCourt(int $courtId, string $fromDate)
    {
        $reservations = DB::table('reservations')
            ->where('court_id', $courtId)
            ->where('reservation_date', '>=', $fromDate)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();
            
        // Convertir a array de objetos estándar
        return json_decode(json_encode($reservations), true);
    }
    
    /**
     * Verifica si una hora específica está reservada
     * @param string $hour Hora a verificar (formato H:i)
     * @param int $dayOfWeek Día de la semana
     * @param int $courtId ID de la cancha
     * @param array $reservations Array de reservas
     * @return bool True si la hora está reservada, false en caso contrario
     */
    private function isHourReserved(string $hour, int $dayOfWeek, int $courtId, array $reservations)
    {
        // Si no hay reservas, no hay nada reservado
        if (empty($reservations)) {
            return false;
        }
        
        // Convertir la hora a formato HH:MM:SS para comparar con la base de datos
        $hourFormatted = $hour . ':00';
        
        // Obtener la fecha actual y calcular la fecha del día de la semana que estamos verificando
        $now = Carbon::now();
        $currentDayOfWeek = $now->dayOfWeek;
        
        // Calcular la fecha para el día de la semana que estamos verificando
        $targetDate = $now->copy();
        if ($dayOfWeek < $currentDayOfWeek) {
            // Si el día es anterior al actual, es de la próxima semana
            $daysToAdd = 7 - ($currentDayOfWeek - $dayOfWeek);
        } else {
            // Si el día es posterior o igual al actual
            $daysToAdd = $dayOfWeek - $currentDayOfWeek;
        }
        $targetDate->addDays($daysToAdd);
        $targetDateStr = $targetDate->toDateString();
        
        // Iterar sobre todas las reservas para esta cancha
        foreach ($reservations as $reservation) {
            // Verificar que la reserva tenga los campos necesarios
            if (!isset($reservation['reservation_date']) || !isset($reservation['start_time']) || !isset($reservation['duration_hours'])) {
                continue;
            }
            
            // Verificar si la reserva es para la fecha objetivo o es una reserva recurrente semanal
            // Primero verificamos si es para la fecha exacta
            $isForTargetDate = ($reservation['reservation_date'] === $targetDateStr);
            
            // Si no es para la fecha exacta, verificamos si es una reserva recurrente para el mismo día de la semana
            if (!$isForTargetDate) {
                $reservationDate = Carbon::parse($reservation['reservation_date']);
                $reservationDayOfWeek = $reservationDate->dayOfWeek;
                
                // Si no es el mismo día de la semana, no aplica
                if ($reservationDayOfWeek != $dayOfWeek) {
                    continue;
                }
                
                // Verificar si la reserva es para una fecha futura (posterior a la fecha objetivo)
                if ($reservationDate->gt($targetDate)) {
                    continue;
                }
            }
            
            // Obtener la hora de inicio y fin de la reserva
            $reservationStart = Carbon::parse($reservation['start_time']);
            $reservationEnd = (clone $reservationStart)->addHours($reservation['duration_hours']);
            
            // Convertir la hora a verificar a un objeto Carbon
            $hourToCheck = Carbon::parse($hourFormatted);
            
            // Verificar si la hora está dentro del rango de la reserva
            // Comparamos solo las horas, no las fechas completas
            if ($hourToCheck->format('H:i:s') >= $reservationStart->format('H:i:s') && 
                $hourToCheck->format('H:i:s') < $reservationEnd->format('H:i:s')) {
                return true; // La hora está reservada
            }
        }
        
        return false; // La hora no está reservada
    }
    
    /**
     * Verifica si un array de horas tiene al menos minContinuousHours horas continuas
     * @param array $hoursArray Array de horas
     * @param int $minContinuousHours Mínimo de horas continuas requeridas
     * @return bool True si hay al menos minContinuousHours horas continuas, false en caso contrario
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
    
    /**
     * Organiza los horarios por día y turno
     * @param mixed $schedules Colección de horarios
     * @param int $currentDayOfWeek Día de la semana actual
     * @param Carbon $currentTime Hora actual
     * @param int $minContinuousHours Mínimo de horas continuas requeridas
     * @return \Illuminate\Support\Collection Colección de horarios organizados por día y turno
     */
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
