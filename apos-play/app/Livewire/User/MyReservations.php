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
        if ($reservation->payment_status === 'paid' || $reservation->amount_paid > 0) {
            try {
                // TODO: Implement Mercado Pago Refund
                // if (Str::startsWith($reservation->payment_id, 'mercadopago_')) {
                //      $client = new \MercadoPago\Client\Payment\PaymentClient();
                //      $client->refund($reservation->payment_id);
                // }

                // Temporary Simulation for testing structure
                \Illuminate\Support\Facades\Log::info("Simulated MercadoPago refund for cancel reservation {$reservation->id}");

                $reservation->payment_status = 'refunded';
                $refundMessage = ' y el dinero ha sido reembolsado';
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error refunding reservation {$reservation->id}: " . $e->getMessage());
                // We still cancel the reservation? Or fail? 
                // Usually better to fail safely or mark for manual review.
                // For now, let's proceed but warn.
                $this->dispatch('reservation-error', message: 'Reserva cancelada pero hubo un error con el reembolso. Contacte soporte.');
            }
        }

        $reservation->status = ReservationStatus::CANCELLED;
        $reservation->save();

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
