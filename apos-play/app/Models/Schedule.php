<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'day_of_week',
        'start_time',
        'end_time',
        'turn',
        'is_available',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_available' => 'boolean',
    ];

    public function schedulesXCourt()
    {
        return $this->hasMany(SchedulesXCourt::class);
    }

    // Scope para filtrar horarios de mañana
    public function scopeMorning($query)
    {
        return $query->where('turn', 'morning');
    }

    // Scope para filtrar horarios de tarde
    public function scopeAfternoon($query)
    {
        return $query->where('turn', 'afternoon');
    }

    public function getAvailableHours(int $minContinuousHours = 1, ?Carbon $fromTime = null): array
    {
        $start = Carbon::createFromFormat('H:i:s', $this->start_time);
        $end = Carbon::createFromFormat('H:i:s', $this->end_time);
        
        // Si se proporciona una hora de inicio, usarla si es después de la hora de inicio del horario
        if ($fromTime && $fromTime->format('H:i:s') > $start->format('H:i:s')) {
            $start = Carbon::createFromFormat('H:i:s', $fromTime->format('H:i:s'));
        }
        
        // Si no hay suficientes horas entre start y end, retornar array vacío
        if ($start->diffInHours($end) < $minContinuousHours) {
            return [];
        }
        
        $hours = [];
        $current = $start->copy();

        // Verificar reservas existentes para este horario en la fecha actual
        $reservations = $this->reservations()
            ->whereDate('reservation_date', Carbon::now()->toDateString())
            ->get()
            ->sortBy('start_time');
        
        // Crear bloques de disponibilidad
        $availabilityBlocks = [];
        $blockStart = $current->copy();
        $blockEnd = $end->copy();
        
        // Ajustar bloques según reservas existentes
        foreach ($reservations as $reservation) {
            $resStart = Carbon::createFromFormat('H:i:s', $reservation->start_time);
            $resEnd = (clone $resStart)->addHours($reservation->duration_hours);
            
            // Si la reserva comienza después del inicio del bloque actual
            if ($resStart > $blockStart) {
                // Agregar bloque desde blockStart hasta resStart
                $availabilityBlocks[] = [
                    'start' => $blockStart->copy(),
                    'end' => $resStart->copy()
                ];
            }
            
            // Actualizar blockStart para después de la reserva
            $blockStart = $resEnd->copy();
        }
        
        // Agregar el último bloque si queda tiempo después de la última reserva
        if ($blockStart < $blockEnd) {
            $availabilityBlocks[] = [
                'start' => $blockStart->copy(),
                'end' => $blockEnd->copy()
            ];
        }
        
        // Filtrar bloques que tengan al menos minContinuousHours de duración
        $availabilityBlocks = array_filter($availabilityBlocks, function($block) use ($minContinuousHours) {
            return $block['start']->diffInHours($block['end']) >= $minContinuousHours;
        });
        
        // Generar horas disponibles a partir de los bloques filtrados
        foreach ($availabilityBlocks as $block) {
            $hourStart = $block['start']->copy();
            while ($hourStart < $block['end']) {
                $hours[] = $hourStart->format('H:i');
                $hourStart->addHour();
            }
        }

        return $hours;
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeForWeek($query, ?int $weekNumber = null)
    {
        return $query->where('is_available', true)
                      ->orderBy('day_of_week')
                      ->orderBy('turn');
    }
}
