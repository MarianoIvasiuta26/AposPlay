<?php

namespace App\Livewire\Admin;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

#[Layout('components.layouts.app')]
class DailyReservations extends Component
{
    public $selectedDate;
    public $reservationToRefund = null;
    public $refundType = null; // 'full' or 'partial'
    public $showRefundModal = false;

    public function mount()
    {
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function confirmRefund($reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);

        // Calculate hours difference for UI
        $dateStr = $reservation->reservation_date instanceof \Carbon\Carbon
            ? $reservation->reservation_date->format('Y-m-d')
            : $reservation->reservation_date;

        $reservationStart = Carbon::parse($dateStr . ' ' . $reservation->start_time);
        $hoursUntilStart = now()->diffInHours($reservationStart, false);

        if ($hoursUntilStart < 2) {
            $this->dispatch('refund-error', message: 'No se puede reembolsar con menos de 2 horas de antelación.');
            return;
        }

        $this->reservationToRefund = $reservation;
        $this->refundType = $hoursUntilStart >= 8 ? 'full' : 'partial';

        $this->dispatch('open-modal', name: 'refund-modal');
    }

    public function processRefund()
    {
        if (!$this->reservationToRefund)
            return;

        $reservation = $this->reservationToRefund;

        // Re-check logic safety
        if (!$reservation->payment_id) {
            $this->dispatch('refund-error', message: 'Esta reserva no tiene un pago asociado.');
            return;
        }

        try {
            $user = $reservation->user;

            $refundAmount = 0;
            $type = '';

            // Recalculate to be safe or use stored type
            if ($this->refundType === 'full') {
                $refundAmount = $reservation->amount_paid;
                $type = 'refunded';
            } else {
                $refundAmount = $reservation->amount_paid * 0.5;
                $type = 'partial_refunded';
            }

            // TODO: Implement Mercado Pago Refund
            // if (Str::startsWith($reservation->payment_id, 'mercadopago_')) {
            //      $client = new \MercadoPago\Client\Payment\PaymentClient();
            //      $client->refund($reservation->payment_id, $refundAmount);
            // }

            // Temporary Simulation for testing structure
            Log::info("Simulated MercadoPago refund of $refundAmount for reservation {$reservation->id}");

            $reservation->update([
                'status' => ReservationStatus::CANCELLED,
                'payment_status' => $type
            ]);

            $this->dispatch('refund-success', message: 'Reembolso exitoso (' . ($type == 'refunded' ? 'Total' : 'Parcial') . ').');

        } catch (\Exception $e) {
            Log::error("Refund failed: " . $e->getMessage());
            $this->dispatch('refund-error', message: 'Error al procesar el reembolso: ' . $e->getMessage());
        }

        $this->reset(['reservationToRefund', 'refundType', 'showRefundModal']);
        $this->dispatch('close-modal', name: 'refund-modal');
    }

    public function refund($reservationId)
    {
        // Legacy method kept if needed or we can replace it.
        // Let's replace usage in blade.
        $this->confirmRefund($reservationId);
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
