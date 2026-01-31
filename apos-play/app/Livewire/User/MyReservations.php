<?php

namespace App\Livewire\User;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class MyReservations extends Component
{
    public function cancel($reservationId)
    {
        $reservation = Reservation::where('user_id', auth()->id())
            ->where('id', $reservationId)
            ->firstOrFail();

        // Check status
        if (!in_array($reservation->status, [ReservationStatus::PENDING->value, ReservationStatus::CONFIRMED->value])) {
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

        $reservation->update(['status' => ReservationStatus::CANCELLED]);

        $this->dispatch('reservation-cancelled', message: 'Reserva cancelada exitosamente.');
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
