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
        if (
            !in_array($reservation->status, [
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

        // If paying, refund logic would be here (for later)

        $reservation->update(['status' => ReservationStatus::CANCELLED]);

        $this->dispatch('reservation-cancelled', message: 'Reserva cancelada exitosamente.');
    }

    public function pay($reservationId)
    {
        $reservation = Reservation::where('user_id', auth()->id())
            ->where('id', $reservationId)
            ->firstOrFail();

        if ($reservation->status !== ReservationStatus::PENDING_PAYMENT->value && $reservation->status !== ReservationStatus::PENDING->value) {
            return;
        }

        // SIMULATED PAYMENT (Since User cannot use Stripe in Argentina)
        // In a real app, this would be MercadoPago SDK or similar.

        $reservation->update([
            'status' => ReservationStatus::CONFIRMED->value,
            'payment_status' => 'paid',
            'payment_id' => 'sim_' . \Illuminate\Support\Str::random(10),
            'amount_paid' => $reservation->total_price
        ]);

        $this->dispatch('reservation-paid', message: 'Reserva pagada exitosamente (Simulación).');

        // Reload to show changes
        return redirect()->route('my-reservations');
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
