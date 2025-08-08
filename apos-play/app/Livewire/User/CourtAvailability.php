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
    
    // Propiedades para el filtro de fecha
    public $selectedDate;
    public $availableDates = [];
    public $courtType = 'all';
    
    public function mount()
    {
        // Usar la fecha de mañana en lugar de hoy, con zona horaria de Argentina
        $this->date = now()->setTimezone('America/Argentina/Buenos_Aires')->addDay();
        $this->dayOfWeek = $this->date->dayOfWeek;
        
        // Inicializar fechas disponibles (próximos 7 días)
        $this->initializeAvailableDates();
        
        // Por defecto, seleccionar mañana
        $this->selectedDate = $this->date->toDateString();
        
        \Illuminate\Support\Facades\Log::info("Fecha seleccionada: {$this->selectedDate}, día de la semana: {$this->dayOfWeek}");
        $this->loadAvailability();
    }
    
    /**
     * Inicializa el array de fechas disponibles para los próximos 7 días
     */
    private function initializeAvailableDates()
    {
        $tomorrow = Carbon::tomorrow()->setTimezone('America/Argentina/Buenos_Aires');
        
        for ($i = 0; $i < 7; $i++) {
            $date = $tomorrow->copy()->addDays($i);
            $this->availableDates[] = [
                'value' => $date->toDateString(),
                'label' => $date->format('d/m/Y') . ' - ' . $this->getDayName($date->dayOfWeek)
            ];
        }
    }

    public function render()
    {   
        // Filtrar canchas por tipo si es necesario
        $filteredCourts = $this->courts;
        if ($this->courtType !== 'all') {
            $filteredCourts = $filteredCourts->filter(function($court) {
                return $court->type === $this->courtType;
            })->values();
        }
        
        return view('livewire.user.court-availability', [
            'filteredCourts' => $filteredCourts
        ]);
    }
    
    /**
     * Actualiza la fecha seleccionada y recarga la disponibilidad
     */
    public function updateSelectedDate($date)
    {
        $this->selectedDate = $date;
        \Illuminate\Support\Facades\Log::info("Nueva fecha seleccionada: {$this->selectedDate}");
        
    }
    
    /**
     * Actualiza el tipo de cancha seleccionado
     */
    public function updateCourtType($type)
    {
        $this->courtType = $type;
        \Illuminate\Support\Facades\Log::info("Nuevo tipo de cancha seleccionado: {$this->courtType}");
    }
    
    /**
     * Restablece los filtros a sus valores predeterminados
     */
    public function resetFilters()
    {
        // Restablecer fecha a mañana
        $this->selectedDate = Carbon::tomorrow()->setTimezone('America/Argentina/Buenos_Aires')->toDateString();
        // Restablecer tipo de cancha a 'all'
        $this->courtType = 'all';
        
        \Illuminate\Support\Facades\Log::info("Filtros restablecidos: fecha={$this->selectedDate}, tipo={$this->courtType}");
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
        $tomorrow = Carbon::tomorrow()->setTimezone('America/Argentina/Buenos_Aires');
        $currentDayOfWeek = $tomorrow->dayOfWeek;
        
        // Depurar la fecha de mañana
        \Illuminate\Support\Facades\Log::info("Fecha de mañana: {$tomorrow->toDateString()}, día de la semana: {$currentDayOfWeek}");
        
        // Agrupar las horas por día de la semana
        $hoursByDay = [];
        foreach ($nonReservedHours as $hourKey => $hourData) {
            $dayOfWeek = $hourData['day_of_week'];
            if (!isset($hoursByDay[$dayOfWeek])) {
                $hoursByDay[$dayOfWeek] = [];
            }
            $hoursByDay[$dayOfWeek][$hourKey] = $hourData;
        }
        
        // Depurar los días de la semana disponibles
        \Illuminate\Support\Facades\Log::info("Días de la semana con horarios: " . implode(", ", array_keys($hoursByDay)));
        if (count($hoursByDay) > 0) {
            $firstDay = array_key_first($hoursByDay);
            \Illuminate\Support\Facades\Log::info("Horarios para el día {$firstDay}: " . count($hoursByDay[$firstDay]));
        }
        
        // Generar disponibilidad para los próximos 7 días, comenzando desde mañana
        for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
            // Calcular la fecha para este día, comenzando desde mañana
            $targetDate = $tomorrow->copy()->addDays($dayOffset);
            $targetDayOfWeek = $targetDate->dayOfWeek;
            $dateStr = $targetDate->toDateString();
            
            \Illuminate\Support\Facades\Log::info("Generando disponibilidad para día {$dayOffset}: {$dateStr}, día de la semana: {$targetDayOfWeek}");
            
            // Si tenemos horarios para este día de la semana
            if (isset($hoursByDay[$targetDayOfWeek])) {
                $hours = $hoursByDay[$targetDayOfWeek];
                
                // Ya no necesitamos filtrar las horas que ya pasaron
                // porque estamos comenzando desde mañana
                
                // Agregar las horas disponibles para este día
                foreach ($hours as $hourKey => $hourData) {
                    try {
                        // Solo agregar las horas si el día de la semana coincide con el día actual que estamos procesando
                        // Esto asegura que los horarios se asignen al día correcto
                        if ($hourData['day_of_week'] == $targetDayOfWeek) {
                            $availableHoursWithDates[] = [
                                'court_id' => $courtId,
                                'hour' => $hourKey,
                                'date' => $dateStr,
                                'day_of_week' => $targetDayOfWeek,
                                'day_name' => $this->getDayName($targetDayOfWeek),
                                'turn' => $hourData['turn'] ?? 'morning', // Valor por defecto si no existe
                                'schedule_id' => $hourData['schedule_id'] ?? 0 // Valor por defecto si no existe
                            ];
                            
                            // Depurar la hora agregada
                            \Illuminate\Support\Facades\Log::info("Agregando hora {$hourKey} para día {$targetDayOfWeek} ({$this->getDayName($targetDayOfWeek)}) con fecha {$dateStr}");
                        }
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
        
        // Depurar las reservas encontradas
        \Illuminate\Support\Facades\Log::info("Reservas encontradas para cancha {$courtId} desde {$fromDate}: " . count($reservations));
        if (count($reservations) > 0) {
            \Illuminate\Support\Facades\Log::info("Primera reserva: " . json_encode($reservations[0]));
        }
            
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
        $tomorrow = Carbon::tomorrow()->setTimezone('America/Argentina/Buenos_Aires');
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
        
        // Depurar información de la fecha objetivo
        \Illuminate\Support\Facades\Log::info("Verificando reservas para fecha: {$targetDateStr}, día de semana: {$dayOfWeek}, hora: {$hour}");
        
        // Iterar sobre todas las reservas para esta cancha
        foreach ($reservations as $reservation) {
            // Verificar que la reserva tenga los campos necesarios
            if (!isset($reservation['reservation_date']) || !isset($reservation['start_time']) || !isset($reservation['duration_hours'])) {
                continue;
            }
            
            // Verificar si la reserva es para la fecha objetivo
            if ($reservation['reservation_date'] !== $targetDateStr) {
                continue; // Si no es para la fecha objetivo, ignorar esta reserva
            }
            
            // Obtener la hora de inicio y fin de la reserva
            $reservationStart = Carbon::parse($reservation['start_time']);
            $reservationEnd = (clone $reservationStart)->addHours($reservation['duration_hours']);
            
            // Convertir la hora a verificar a un objeto Carbon
            $hourToCheck = Carbon::parse($hourFormatted);
            
            // Verificar si la hora está dentro del rango de la reserva
            // Comparamos solo las horas, no las fechas completas
            $hourToCheckFormatted = $hourToCheck->format('H:i:s');
            $reservationStartFormatted = $reservationStart->format('H:i:s');
            $reservationEndFormatted = $reservationEnd->format('H:i:s');
            
            \Illuminate\Support\Facades\Log::info("Comparando hora {$hourToCheckFormatted} con reserva {$reservation['id']} de {$reservationStartFormatted} a {$reservationEndFormatted}");
            
            if ($hourToCheckFormatted >= $reservationStartFormatted && 
                $hourToCheckFormatted < $reservationEndFormatted) {
                \Illuminate\Support\Facades\Log::info("Hora {$hour} RESERVADA para fecha {$targetDateStr}");
                return true; // La hora está reservada
            }
        }
        
        return false; // La hora no está reservada
    }
    
    /**
     * Organiza las horas disponibles por fecha
     * @param array $availableHours Array de horas disponibles
     * @return array Array de horas organizadas por fecha
     */
    private function organizeHoursByDate(array $availableHours): array
    {
        $hoursByDate = [];
        
        // Depurar los datos de entrada
        \Illuminate\Support\Facades\Log::info("organizeHoursByDate - Cantidad de horas disponibles: " . count($availableHours));
        if (count($availableHours) > 0) {
            \Illuminate\Support\Facades\Log::info("Primera hora disponible: " . json_encode($availableHours[0]));
        }
        
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
