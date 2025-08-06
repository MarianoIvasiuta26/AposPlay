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
    public $hoursXCourts;
    
    public function mount()
    {
        // Usar la fecha de mañana en lugar de hoy
        $this->date = now()->addDay();
        $this->dayOfWeek = $this->date->dayOfWeek;
        $this->loadAvailability();
    }

    public function render()
    {
        // Si no hay horas disponibles para ninguna cancha, agregar datos de ejemplo
        $hasAvailableHours = false;
        foreach ($this->hoursXCourts as $courtId => $hours) {
            if (!empty($hours)) {
                $hasAvailableHours = true;
                break;
            }
        }
        
        // if (!$hasAvailableHours) {
        //     // Agregar datos de ejemplo para todas las canchas
        //     foreach ($this->courts as $court) {
        //         $tomorrow = \Carbon\Carbon::tomorrow();
        //         $date = $tomorrow->toDateString();
        //         $dayName = $this->getDayName($tomorrow->dayOfWeek);
                
        //         // Crear horas de ejemplo para esta cancha
        //         $this->hoursXCourts[$court->id] = [
        //             $date => [
        //                 'day_name' => $dayName,
        //                 'date' => $date,
        //                 'hours' => [
        //                     ['hour' => '09:00', 'court_id' => $court->id],
        //                     ['hour' => '10:00', 'court_id' => $court->id],
        //                     ['hour' => '16:00', 'court_id' => $court->id],
        //                     ['hour' => '17:00', 'court_id' => $court->id],
        //                     ['hour' => '18:00', 'court_id' => $court->id],
        //                     ['hour' => '20:00', 'court_id' => $court->id],
        //                 ]
        //             ]
        //         ];
        //     }
            
        //     // Agregar un mensaje de log para indicar que se están usando datos de ejemplo
        //     \Illuminate\Support\Facades\Log::warning('No se encontraron horas disponibles reales. Usando datos de ejemplo.');
        // }
        
        return view('livewire.user.court-availability');
    }

    public function loadAvailability(int $minContinuousHours = 0) // Cambiado a 0 para mostrar todas las canchas
    {
        // Usar la fecha de mañana
        $tomorrow = Carbon::tomorrow();
        $currentDayOfWeek = $tomorrow->dayOfWeek;
        $currentTime = $tomorrow->copy()->startOfDay(); // Comenzar desde el inicio del día
        
        // Obtener todas las canchas con sus direcciones
        $this->courts = DB::table('courts')
            ->select('courts.*', 'court_addresses.city as location')
            ->join('court_addresses', 'courts.court_address_id', '=', 'court_addresses.id')
            ->get();
        
        // Inicializar el array de horas disponibles con fechas
        $this->available_hours_with_dates = [];
        $this->hoursXCourts = [];

        // Para cada cancha, generar todas las horas posibles y filtrar las reservadas
        foreach ($this->courts as $court) {
            // Cargar los horarios de la cancha
            $schedules = $this->loadSchedule($court->id);
            // Generar horas no reservadas
            $nonReservedHours = $this->generateNonReservedHoursArray($schedules, $court->id);
            
            // Generar y guardar el array de horas disponibles con fechas
            $this->available_hours_with_dates[$court->id] = $this->generateAvailableHoursWithDates($nonReservedHours, $court->id);
            
            // Organizar horas por fecha para esta cancha
            $this->hoursXCourts[$court->id] = $this->organizeHoursByDate($this->available_hours_with_dates[$court->id]);
        }
        
        // No filtramos las canchas, mostramos todas
        // $this->courts ya contiene todas las canchas desde la consulta inicial
    }

    public function loadSchedule(int $courtId)
    {
        // Consulta SQL para obtener los horarios directamente
        $scheduleIds = DB::table('schedules_x_courts')
            ->where('court_id', $courtId)
            ->pluck('schedule_id');
        
        $schedules = DB::table('schedules')
            ->whereIn('id', $scheduleIds)
            ->where('is_available', 1) // Cambiado a 1 porque todos los registros tienen is_available = 1
            ->orderBy('day_of_week')
            ->orderBy('turn')
            ->get();
        
        // Depurar los horarios cargados
        \Illuminate\Support\Facades\Log::info("Horarios cargados para cancha {$courtId}: " . count($schedules));
        if (count($schedules) > 0) {
            \Illuminate\Support\Facades\Log::info("Primer horario: " . json_encode($schedules[0]));
        }
        
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
            if ($schedule->is_available == 1) { // 0 = disponible, 1 = no disponible
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
        $tomorrow = Carbon::tomorrow();
        $currentDate = $tomorrow->toDateString(); // Usar la fecha de mañana
        // Depurar la cantidad de horarios recibidos
        \Illuminate\Support\Facades\Log::info("generateNonReservedHoursArray - Horarios recibidos para cancha {$courtId}: " . count($schedules));
        
        // Obtener las reservas existentes para esta cancha
        $reservations = $this->getReservationsForCourt($courtId, $currentDate);
        \Illuminate\Support\Facades\Log::info("Reservas encontradas para cancha {$courtId}: " . count($reservations));
        
        // Generar el array de todas las horas disponibles según los horarios
        $allHours = $this->generateHoursArray($schedules, $courtId);
        \Illuminate\Support\Facades\Log::info("Horas generadas para cancha {$courtId}: " . count($allHours));
        
        // Filtrar las horas que no están reservadas
        foreach ($allHours as $hourKey => $hourData) {
            $isReserved = $this->isHourReserved($hourKey, $hourData['day_of_week'], $courtId, $reservations);
            
            if (!$isReserved) {
                $nonReservedHours[$hourKey] = $hourData;
            }
        }
        
        \Illuminate\Support\Facades\Log::info("Horas NO reservadas para cancha {$courtId}: " . count($nonReservedHours));
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
        // Usar mañana como fecha de inicio
        $tomorrow = Carbon::tomorrow();
        $currentDayOfWeek = $tomorrow->dayOfWeek;
        
        // Agrupar las horas por día de la semana
        $hoursByDay = [];
        foreach ($nonReservedHours as $hourKey => $hourData) {
            $dayOfWeek = $hourData['day_of_week'];
            if (!isset($hoursByDay[$dayOfWeek])) {
                $hoursByDay[$dayOfWeek] = [];
            }
            $hoursByDay[$dayOfWeek][$hourKey] = $hourData;
        }
        
        // Generar disponibilidad para los próximos 7 días, comenzando desde mañana
        for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
            // Calcular la fecha para este día, comenzando desde mañana
            $targetDate = $tomorrow->copy()->addDays($dayOffset);
            $targetDayOfWeek = $targetDate->dayOfWeek;
            $dateStr = $targetDate->toDateString();
            
            // Si tenemos horarios para este día de la semana
            if (isset($hoursByDay[$targetDayOfWeek])) {
                $hours = $hoursByDay[$targetDayOfWeek];
                
                // Ya no necesitamos filtrar las horas que ya pasaron
                // porque estamos comenzando desde mañana
                
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
        
        // Obtener la fecha de mañana y calcular la fecha del día de la semana que estamos verificando
        $tomorrow = Carbon::tomorrow();
        $currentDayOfWeek = $tomorrow->dayOfWeek;
        
        // Calcular la fecha para el día de la semana que estamos verificando
        $targetDate = $tomorrow->copy();
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
            if (!isset($reservation->reservation_date) || !isset($reservation->start_time) || !isset($reservation->duration_hours)) {
                continue;
            }
            
            // Verificar si la reserva es para la fecha objetivo o es una reserva recurrente semanal
            // Primero verificamos si es para la fecha exacta
            $isForTargetDate = ($reservation->reservation_date === $targetDateStr);
            
            // Si no es para la fecha exacta, verificamos si es una reserva recurrente para el mismo día de la semana
            if (!$isForTargetDate) {
                $reservationDate = Carbon::parse($reservation->reservation_date);
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
            $reservationStart = Carbon::parse($reservation->start_time);
            $reservationEnd = (clone $reservationStart)->addHours($reservation->duration_hours);
            
            // Convertir la hora a verificar a un objeto Carbon
            $hourToCheck = Carbon::parse($hourFormatted);
            
            // Verificar si la hora está dentro del rango de la reserva
            // Comparamos solo las horas, no las fechas completas
            // Aseguramos que todos los formatos sean H:i:s para la comparación
            $hourToCheckFormatted = $hourToCheck->format('H:i') . ':00';
            $reservationStartFormatted = $reservationStart->format('H:i:s');
            $reservationEndFormatted = $reservationEnd->format('H:i:s');
            
            if ($hourToCheckFormatted >= $reservationStartFormatted && 
                $hourToCheckFormatted < $reservationEndFormatted) {
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
    
    /**
     * Organiza las horas disponibles por fecha
     * @param array $availableHours Array de horas disponibles
     * @return array Array de horas organizadas por fecha
     */
    private function organizeHoursByDate(array $availableHours): array
    {
        $hoursByDate = [];
        
        foreach ($availableHours as $hourData) {
            $date = $hourData['date'];
            
            if (!isset($hoursByDate[$date])) {
                $hoursByDate[$date] = [
                    'day_name' => $hourData['day_name'],
                    'date' => $date,
                    'hours' => []
                ];
            }
            
            $hoursByDate[$date]['hours'][] = $hourData;
        }
        
        // Ordenar por fecha
        ksort($hoursByDate);
        
        return $hoursByDate;
    }
}
