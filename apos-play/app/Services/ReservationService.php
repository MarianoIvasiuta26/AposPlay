<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function __construct(
        protected CourtBlockService $courtBlockService
    ) {}

    public function canReschedule(Reservation $reservation): bool
    {
        $allowedStatuses = [
            ReservationStatus::PENDING,
            ReservationStatus::PENDING_PAYMENT,
            ReservationStatus::CONFIRMED,
            ReservationStatus::PAID,
        ];

        if (!in_array($reservation->status, $allowedStatuses)) {
            return false;
        }

        $dateStr = $reservation->reservation_date instanceof Carbon
            ? $reservation->reservation_date->format('Y-m-d')
            : $reservation->reservation_date;

        $reservationStart = Carbon::parse($dateStr . ' ' . $reservation->start_time);

        return now()->addHours(4)->lte($reservationStart);
    }

    public function reschedule(Reservation $reservation, string $newDate, string $newTime, int $newDuration): array
    {
        if (!$this->canReschedule($reservation)) {
            return ['success' => false, 'message' => 'Esta reserva no puede ser reprogramada.'];
        }

        // If reservation is PAID, only allow same duration
        if ($reservation->status === ReservationStatus::PAID && $reservation->duration_hours != $newDuration) {
            return ['success' => false, 'message' => 'Las reservas pagadas solo pueden reprogramarse con la misma duracion. Para cambiar la duracion, cancela y reserva de nuevo.'];
        }

        // Check availability of new slot
        $startTime = Carbon::parse($newTime);

        for ($i = 0; $i < $newDuration; $i++) {
            $checkTime = $startTime->copy()->addHours($i)->format('H:i');

            if ($this->courtBlockService->isSlotBlocked($reservation->court_id, $newDate, $checkTime)) {
                return ['success' => false, 'message' => "El horario de las {$checkTime} está bloqueado."];
            }
        }

        // Check existing reservations (exclude current one)
        $existingReservations = DB::table('reservations')
            ->where('court_id', $reservation->court_id)
            ->where('reservation_date', $newDate)
            ->where('id', '!=', $reservation->id)
            ->whereIn('status', ['pending', 'pending_payment', 'confirmed', 'paid'])
            ->whereNull('deleted_at')
            ->get(['start_time', 'duration_hours'])
            ->toArray();

        for ($i = 0; $i < $newDuration; $i++) {
            $checkTime = $startTime->copy()->addHours($i)->format('H:i');

            foreach ($existingReservations as $existing) {
                $resStart = Carbon::parse($existing->start_time);
                $resEnd = $resStart->copy()->addHours((int) $existing->duration_hours);
                $slot = Carbon::parse($checkTime);

                if ($slot >= $resStart && $slot < $resEnd) {
                    return ['success' => false, 'message' => "El horario de las {$checkTime} ya está reservado."];
                }
            }
        }

        return DB::transaction(function () use ($reservation, $newDate, $newTime, $newDuration) {
            $reservation->update([
                'reservation_date' => $newDate,
                'start_time' => $newTime,
                'duration_hours' => $newDuration,
            ]);

            return ['success' => true, 'message' => 'Reserva reprogramada exitosamente.'];
        });
    }

    public function getAvailableSlotsForDate(int $courtId, string $date): array
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        $schedules = DB::table('schedules')
            ->join('schedules_x_courts', 'schedules.id', '=', 'schedules_x_courts.schedule_id')
            ->where('schedules_x_courts.court_id', $courtId)
            ->where('schedules.day_of_week', $dayOfWeek)
            ->where('schedules.is_available', 1)
            ->whereNull('schedules.deleted_at')
            ->select('schedules.*')
            ->get();

        $reservations = DB::table('reservations')
            ->where('court_id', $courtId)
            ->where('reservation_date', $date)
            ->whereIn('status', ['pending', 'pending_payment', 'confirmed', 'paid'])
            ->whereNull('deleted_at')
            ->get(['start_time', 'duration_hours'])
            ->toArray();

        $slots = [];
        $seenHours = [];

        foreach ($schedules as $schedule) {
            $current = Carbon::parse($schedule->start_time);
            $end = Carbon::parse($schedule->end_time);

            while ($current < $end) {
                $hourStr = $current->format('H:i');

                if (!isset($seenHours[$hourStr])) {
                    $seenHours[$hourStr] = true;

                    $isBlocked = $this->courtBlockService->isSlotBlocked($courtId, $date, $hourStr);
                    $isTaken = false;

                    if (!$isBlocked) {
                        foreach ($reservations as $res) {
                            $resStart = Carbon::parse($res->start_time);
                            $resEnd = $resStart->copy()->addHours((int) $res->duration_hours);
                            $slot = Carbon::parse($hourStr);
                            if ($slot >= $resStart && $slot < $resEnd) {
                                $isTaken = true;
                                break;
                            }
                        }
                    }

                    if (!$isBlocked && !$isTaken) {
                        $slots[] = $hourStr;
                    }
                }

                $current->addHour();
            }
        }

        sort($slots);
        return $slots;
    }
}
