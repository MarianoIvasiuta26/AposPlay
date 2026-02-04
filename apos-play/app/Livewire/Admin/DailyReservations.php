<?php

namespace App\Livewire\Admin;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.app')]
class DailyReservations extends Component
{
    public $selectedDate;

    public function mount()
    {
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function refund($reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);

        // Calculate hours difference
        $dateStr = $reservation->reservation_date instanceof \Carbon\Carbon
            ? $reservation->reservation_date->format('Y-m-d')
            : $reservation->reservation_date;

        $reservationStart = Carbon::parse($dateStr . ' ' . $reservation->start_time);
        $hoursUntilStart = now()->diffInHours($reservationStart, false);

        if ($hoursUntilStart < 2) {
            $this->dispatch('refund-error', message: 'No se puede reembolsar con menos de 2 horas de antelación.');
            return;
        }

        if (!$reservation->payment_id) {
            $this->dispatch('refund-error', message: 'Esta reserva no tiene un pago asociado.');
            return;
        }

        try {
            $user = $reservation->user; // The user who paid

            // Logic: > 8 hours = Full, 2-8 hours = Partial (50%)
            $refundAmount = 0;
            $refundType = '';

            if ($hoursUntilStart >= 8) {
                $refundAmount = $reservation->amount_paid;
                $refundType = 'refunded'; // Full
            } else {
                $refundAmount = $reservation->amount_paid * 0.5;
                $refundType = 'partial_refunded';
            }

            // SIMULACION or STRIPE
            if (Str::startsWith($reservation->payment_id, 'sim_')) {
                // Mock Refund
                Log::info("Simulated refund of $refundAmount for reservation {$reservation->id}");
            } else {
                // Stripe Refund
                if ($refundType == 'refunded') {
                    $user->refund($reservation->payment_id);
                } else {
                    $user->refund($reservation->payment_id, ['amount' => (int) ($refundAmount * 100)]);
                }
            }

            $reservation->update([
                'status' => ReservationStatus::CANCELLED,
                'payment_status' => $refundType
            ]);

            $this->dispatch('refund-success', message: 'Reembolso exitoso (' . ($refundType == 'refunded' ? 'Total' : 'Parcial') . ').');

        } catch (\Exception $e) {
            Log::error("Refund failed: " . $e->getMessage());
            $this->dispatch('refund-error', message: 'Error al procesar el reembolso: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $reservations = Reservation::whereDate('reservation_date', $this->selectedDate)
            ->with(['user', 'court'])
            ->orderBy('start_time')
            ->get();

        return view('livewire.admin.daily-reservations', [
            'reservations' => $reservations
        ]);
    }
}
