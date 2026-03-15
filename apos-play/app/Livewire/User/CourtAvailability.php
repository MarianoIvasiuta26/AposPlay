<?php

namespace App\Livewire\User;

use App\Models\Coupon;
use App\Services\CourtBlockService;
use App\Services\LoyaltyService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CourtAvailability extends Component
{
    public $courts;
    public $hoursXCourts = [];

    // Filtros
    public $selectedDate;
    public $availableDates = [];
    public $courtType = 'all';

    // Modal de reserva
    public $showReservationModal = false;
    public $reservationCourtId;
    public $reservationScheduleId;
    public $reservationDate;
    public $reservationTime;
    public $reservationCourtName;
    public $reservationDuration = 1;
    public $reservationPrice;

    // Cupón
    public string $couponCode = '';
    public ?Coupon $appliedCoupon = null;
    public float $discountAmount = 0;

    // Puntos de fidelidad
    public bool $usePoints = false;
    public int $userPointsBalance = 0;

    public function mount()
    {
        $this->initializeAvailableDates();
        $this->selectedDate = Carbon::tomorrow()
            ->setTimezone('America/Argentina/Buenos_Aires')
            ->toDateString();
        $this->loadAvailability();
    }

    private function initializeAvailableDates()
    {
        $tomorrow = Carbon::tomorrow()->setTimezone('America/Argentina/Buenos_Aires');

        for ($i = 0; $i < 7; $i++) {
            $date = $tomorrow->copy()->addDays($i);
            $this->availableDates[] = [
                'value' => $date->toDateString(),
                'label' => $date->format('d/m/Y') . ' - ' . $this->getDayName($date->dayOfWeek),
            ];
        }
    }

    public function render()
    {
        $filteredCourts = $this->courts;
        if ($this->courtType !== 'all') {
            $filteredCourts = $filteredCourts->filter(
                fn($court) => $court->type === $this->courtType
            )->values();
        }

        return view('livewire.user.court-availability', [
            'filteredCourts' => $filteredCourts,
        ]);
    }

    public function updateSelectedDate($date)
    {
        $this->selectedDate = $date;
    }

    public function updateCourtType($type)
    {
        $this->courtType = $type;
    }

    public function resetFilters()
    {
        $this->selectedDate = Carbon::tomorrow()
            ->setTimezone('America/Argentina/Buenos_Aires')
            ->toDateString();
        $this->courtType = 'all';
    }

    // -------------------------------------------------------------------------
    // Modal de reserva
    // -------------------------------------------------------------------------

    public function openReservationModal($courtId, $scheduleId, $date, $time)
    {
        $this->reservationCourtId  = $courtId;
        $this->reservationScheduleId = $scheduleId;
        $this->reservationDate     = $date;
        $this->reservationTime     = $time;

        $court = $this->courts->firstWhere('id', $courtId);
        $this->reservationCourtName = $court ? $court->name : 'Cancha';
        $this->reservationPrice     = $court ? $court->price : 0;

        $this->userPointsBalance = app(LoyaltyService::class)->getBalance(auth()->user());
        $this->usePoints = false;

        $this->showReservationModal = true;
    }

    public function closeReservationModal()
    {
        $this->showReservationModal = false;
        $this->reset([
            'reservationCourtId', 'reservationScheduleId', 'reservationDate',
            'reservationTime', 'reservationCourtName', 'reservationPrice', 'reservationDuration',
            'usePoints', 'userPointsBalance',
        ]);
        $this->resetCoupon();
    }

    // -------------------------------------------------------------------------
    // Cupón
    // -------------------------------------------------------------------------

    public function applyCoupon()
    {
        $this->resetErrorBag('couponCode');

        $code = trim($this->couponCode);
        if (empty($code)) {
            $this->addError('couponCode', 'Ingresá un código de cupón.');
            return;
        }

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon || !$coupon->isValid()) {
            $this->addError('couponCode', 'El cupón no es válido o está vencido.');
            return;
        }

        $assigned = $coupon->users()
            ->where('user_id', auth()->id())
            ->whereNull('coupon_user.used_at')
            ->exists();

        if (!$assigned) {
            $this->addError('couponCode', 'Este cupón no está disponible para tu cuenta.');
            return;
        }

        $courtBelongsToCreator = DB::table('courts_x_admins')
            ->where('court_id', $this->reservationCourtId)
            ->where('user_id', $coupon->created_by)
            ->exists();

        if (!$courtBelongsToCreator) {
            $this->addError('couponCode', 'Este cupón no aplica para la cancha seleccionada.');
            return;
        }

        $subtotal = $this->reservationPrice * $this->reservationDuration;
        $this->appliedCoupon  = $coupon;
        $this->discountAmount = $coupon->calculateDiscount($subtotal);
    }

    public function removeCoupon()
    {
        $this->resetCoupon();
    }

    private function resetCoupon()
    {
        $this->couponCode     = '';
        $this->appliedCoupon  = null;
        $this->discountAmount = 0;
        $this->resetErrorBag('couponCode');
    }

    // -------------------------------------------------------------------------
    // Confirmación de reserva
    // -------------------------------------------------------------------------

    public function confirmReservation()
    {
        $this->validate([
            'reservationDuration' => 'required|integer|min:1|max:4',
        ]);

        try {
            $schedule = DB::table('schedules')
                ->where('id', $this->reservationScheduleId)
                ->first();

            if (!$schedule) {
                session()->flash('error', 'Horario no válido.');
                $this->closeReservationModal();
                return;
            }

            $startTime      = Carbon::parse($this->reservationTime);
            $endTime        = $startTime->copy()->addHours((int) $this->reservationDuration);
            $scheduleEndTime = Carbon::parse($schedule->end_time);

            if (
                $scheduleEndTime->format('H:i') !== '00:00' &&
                $endTime->format('H:i') > $scheduleEndTime->format('H:i')
            ) {
                session()->flash('error', 'La duración seleccionada excede el horario de cierre de la cancha.');
                return;
            }

            // Validar disponibilidad hora por hora en la fecha exacta
            $existingReservations = $this->fetchReservationsForDate(
                $this->reservationCourtId,
                $this->reservationDate
            );

            $blockService = app(CourtBlockService::class);

            for ($i = 0; $i < $this->reservationDuration; $i++) {
                $checkTime = $startTime->copy()->addHours($i)->format('H:i');

                if ($blockService->isSlotBlocked($this->reservationCourtId, $this->reservationDate, $checkTime)) {
                    session()->flash('error', "El horario de las {$checkTime} está bloqueado y no disponible.");
                    $this->closeReservationModal();
                    $this->loadAvailability();
                    return;
                }

                if ($this->isSlotTaken($checkTime, $existingReservations)) {
                    session()->flash('error', "El horario de las {$checkTime} ya está reservado. Seleccioná una menor duración u otro horario.");
                    $this->closeReservationModal();
                    $this->loadAvailability();
                    return;
                }
            }

            // Calcular precio con descuento
            $subtotal = $this->reservationPrice * $this->reservationDuration;
            $couponDiscount = $this->appliedCoupon
                ? $this->appliedCoupon->calculateDiscount($subtotal)
                : 0;
            $afterCoupon = max(0, $subtotal - $couponDiscount);

            // Calcular descuento por puntos
            $loyaltyService = app(LoyaltyService::class);
            $pointsRequired = config('loyalty.points_for_discount');
            $pointsDiscount = 0;
            $pointsRedeemed = 0;

            if ($this->usePoints && $loyaltyService->canRedeem(auth()->user(), $pointsRequired)) {
                $pointsDiscount = round($subtotal * (config('loyalty.discount_percentage') / 100), 2);
                $pointsRedeemed = $pointsRequired;
            }

            $finalPrice = max(0, $afterCoupon - $pointsDiscount);

            $reservation = \App\Models\Reservation::create([
                'user_id'         => auth()->id(),
                'court_id'        => $this->reservationCourtId,
                'schedule_id'     => $this->reservationScheduleId,
                'reservation_date' => $this->reservationDate,
                'start_time'      => $this->reservationTime,
                'duration_hours'  => $this->reservationDuration,
                'status'          => 'pending',
                'total_price'     => $afterCoupon,
                'coupon_id'       => $this->appliedCoupon?->id,
                'discount_amount' => $couponDiscount,
                'points_redeemed' => $pointsRedeemed,
                'points_discount' => $pointsDiscount,
                'final_price'     => $finalPrice,
            ]);

            if ($this->appliedCoupon) {
                $this->appliedCoupon->increment('times_used');
                $this->appliedCoupon->users()->updateExistingPivot(auth()->id(), [
                    'used_at' => now(),
                ]);
            }

            if ($pointsRedeemed > 0) {
                $loyaltyService->redeemPoints(auth()->user(), $reservation, $pointsRedeemed);
            }

            session()->flash('success', '¡Reserva realizada con éxito!');
            $this->closeReservationModal();
            $this->loadAvailability();

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al crear reserva: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al procesar la reserva. Por favor intente nuevamente.');
        }
    }

    // -------------------------------------------------------------------------
    // Carga de disponibilidad (fecha a fecha, sin abstracciones de día-de-semana)
    // -------------------------------------------------------------------------

    public function loadAvailability()
    {
        $this->courts = DB::table('courts')
            ->select('courts.*', 'court_addresses.city as location')
            ->join('court_addresses', 'courts.court_address_id', '=', 'court_addresses.id')
            ->whereNull('courts.deleted_at')
            ->get();

        $this->hoursXCourts = [];

        $tomorrow = Carbon::tomorrow()->setTimezone('America/Argentina/Buenos_Aires');

        foreach ($this->courts as $court) {
            $this->hoursXCourts[$court->id] = [];

            for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
                $date       = $tomorrow->copy()->addDays($dayOffset);
                $dateStr    = $date->toDateString();
                $dayOfWeek  = $date->dayOfWeek;

                // Horarios del día (morning + afternoon)
                $schedules = DB::table('schedules')
                    ->join('schedules_x_courts', 'schedules.id', '=', 'schedules_x_courts.schedule_id')
                    ->where('schedules_x_courts.court_id', $court->id)
                    ->where('schedules.day_of_week', $dayOfWeek)
                    ->where('schedules.is_available', 1)
                    ->whereNull('schedules.deleted_at')
                    ->select('schedules.*')
                    ->get();

                if ($schedules->isEmpty()) {
                    continue;
                }

                // Reservas confirmadas/pagadas para esta cancha en esta fecha
                $reservations = $this->fetchReservationsForDate($court->id, $dateStr);

                $hours    = [];
                $seenHours = [];

                foreach ($schedules as $schedule) {
                    $current = Carbon::parse($schedule->start_time);
                    $end     = Carbon::parse($schedule->end_time);

                    while ($current < $end) {
                        $hourStr = $current->format('H:i');

                        if (!isset($seenHours[$hourStr])) {
                            $seenHours[$hourStr] = true;

                            $blockService = app(CourtBlockService::class);
                            $isBlocked = $blockService->isSlotBlocked($court->id, $dateStr, $hourStr);

                            if ($isBlocked) {
                                $status = 'blocked';
                            } elseif ($this->isSlotTaken($hourStr, $reservations)) {
                                $status = 'reserved';
                            } else {
                                $status = 'available';
                            }

                            $hours[] = [
                                'hour'        => $hourStr,
                                'date'        => $dateStr,
                                'day_of_week' => $dayOfWeek,
                                'turn'        => $schedule->turn,
                                'schedule_id' => $schedule->id,
                                'status'      => $status,
                            ];
                        }

                        $current->addHour();
                    }
                }

                if (!empty($hours)) {
                    // Ordenar por hora
                    usort($hours, fn($a, $b) => $a['hour'] <=> $b['hour']);

                    $this->hoursXCourts[$court->id][$dateStr] = [
                        'day_name' => $this->getDayName($dayOfWeek),
                        'date'     => $dateStr,
                        'hours'    => $hours,
                    ];
                }
            }
        }
    }

    // -------------------------------------------------------------------------
    // Helpers privados
    // -------------------------------------------------------------------------

    /**
     * Devuelve las reservas activas para una cancha en una fecha concreta.
     * Incluye todos los estados que bloquean el slot.
     */
    private function fetchReservationsForDate(int $courtId, string $date): array
    {
        return DB::table('reservations')
            ->where('court_id', $courtId)
            ->where('reservation_date', $date)
            ->whereIn('status', ['pending', 'pending_payment', 'confirmed', 'paid'])
            ->whereNull('deleted_at')
            ->get(['start_time', 'duration_hours'])
            ->toArray();
    }

    /**
     * Indica si una hora (formato H:i) está cubierta por alguna reserva existente.
     */
    private function isSlotTaken(string $hour, array $reservations): bool
    {
        foreach ($reservations as $reservation) {
            $resStart = Carbon::parse($reservation->start_time ?? $reservation['start_time']);
            $resEnd   = $resStart->copy()->addHours(
                (int) ($reservation->duration_hours ?? $reservation['duration_hours'])
            );

            $slot = Carbon::parse($hour);

            if ($slot >= $resStart && $slot < $resEnd) {
                return true;
            }
        }

        return false;
    }

    private function getDayName(int $dayOfWeek): string
    {
        return [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ][$dayOfWeek] ?? 'Desconocido';
    }
}
