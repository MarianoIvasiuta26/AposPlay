<?php

namespace App\Livewire\User;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Services\RefundService;
use App\Services\ReservationService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class MyReservations extends Component
{
    // Reschedule modal
    public bool $showRescheduleModal = false;
    public ?int $rescheduleReservationId = null;
    public string $newDate = '';
    public string $newTime = '';
    public int $newDuration = 1;
    public array $availableSlots = [];
    public array $availableDates = [];

    public function cancel($reservationId)
    {
        $reservation = Reservation::where('user_id', auth()->id())
            ->where('id', $reservationId)
            ->firstOrFail();

        // Check status
        if (
            !in_array($reservation->status->value, [
                ReservationStatus::PENDING->value,
                ReservationStatus::PENDING_PAYMENT->value,
                ReservationStatus::CONFIRMED->value
            ])
        ) {
            return;
        }

        // Check time limit (24 hours before)
        $dateStr = $reservation->reservation_date instanceof \Carbon\Carbon
            ? $reservation->reservation_date->format('Y-m-d')
            : $reservation->reservation_date;

        $reservationStart = Carbon::parse($dateStr . ' ' . $reservation->start_time);

        if (now()->addHours(24)->gt($reservationStart)) {
            $this->dispatch('reservation-error', message: 'No se puede cancelar con menos de 24 horas de antelación.');
            return;
        }

        // Refund Logic
        $refundMessage = '';
        if (($reservation->payment_status === 'paid' || $reservation->amount_paid > 0) && $reservation->payment_id) {
            $refundService = app(RefundService::class);
            $result = $refundService->processRefund($reservation, 'full');

            if ($result['success']) {
                $refundMessage = ' y el dinero ha sido reembolsado';
            } else {
                $this->dispatch('reservation-error', message: 'Reserva cancelada pero hubo un error con el reembolso. Contacte soporte.');
                return;
            }
        } else {
            $reservation->status = ReservationStatus::CANCELLED;
            $reservation->save();
        }

        $this->dispatch('reservation-cancelled', message: 'Reserva cancelada exitosamente' . $refundMessage . '.');
    }

    public function pay($reservationId)
    {
        $reservation = Reservation::where('user_id', auth()->id())
            ->where('id', $reservationId)
            ->firstOrFail();

        if ($reservation->status->value !== ReservationStatus::PENDING_PAYMENT->value && $reservation->status->value !== ReservationStatus::PENDING->value) {
            return;
        }

        return redirect()->route('mercadopago.create', ['reservation' => $reservation->id]);
    }

    public function openRescheduleModal(int $reservationId)
    {
        $reservation = Reservation::where('user_id', auth()->id())
            ->where('id', $reservationId)
            ->firstOrFail();

        $reservationService = app(ReservationService::class);

        if (!$reservationService->canReschedule($reservation)) {
            session()->flash('error', 'Esta reserva no puede ser reprogramada.');
            return;
        }

        $this->rescheduleReservationId = $reservation->id;
        $this->newDuration = $reservation->duration_hours ?? 1;
        $this->showRescheduleModal = true;

        // Build available dates (next 7 days from tomorrow)
        $tomorrow = Carbon::tomorrow()->setTimezone('America/Argentina/Buenos_Aires');
        $this->availableDates = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $tomorrow->copy()->addDays($i);
            $this->availableDates[] = [
                'value' => $date->toDateString(),
                'label' => $date->format('d/m/Y'),
            ];
        }

        $this->newDate = $this->availableDates[0]['value'] ?? '';
        $this->loadAvailableSlots();
    }

    public function loadAvailableSlots()
    {
        if (empty($this->newDate) || !$this->rescheduleReservationId) {
            $this->availableSlots = [];
            return;
        }

        $reservation = Reservation::find($this->rescheduleReservationId);
        if (!$reservation) return;

        $reservationService = app(ReservationService::class);
        $this->availableSlots = $reservationService->getAvailableSlotsForDate(
            $reservation->court_id,
            $this->newDate
        );

        $this->newTime = $this->availableSlots[0] ?? '';
    }

    public function updatedNewDate()
    {
        $this->loadAvailableSlots();
    }

    public function confirmReschedule()
    {
        if (!$this->rescheduleReservationId) return;

        $reservation = Reservation::where('user_id', auth()->id())
            ->where('id', $this->rescheduleReservationId)
            ->firstOrFail();

        $reservationService = app(ReservationService::class);
        $result = $reservationService->reschedule(
            $reservation,
            $this->newDate,
            $this->newTime,
            $this->newDuration
        );

        if ($result['success']) {
            session()->flash('success', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }

        $this->closeRescheduleModal();
    }

    public function closeRescheduleModal()
    {
        $this->showRescheduleModal = false;
        $this->reset(['rescheduleReservationId', 'newDate', 'newTime', 'newDuration', 'availableSlots', 'availableDates']);
    }

    public function render()
    {
        $reservations = Reservation::where('user_id', auth()->id())
            ->with(['court.address'])
            ->orderByDesc('reservation_date')
            ->orderByDesc('start_time')
            ->get();

        return view('livewire.user.my-reservations', [
            'reservations' => $reservations
        ]);
    }
}
