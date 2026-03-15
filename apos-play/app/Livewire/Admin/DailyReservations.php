<?php

namespace App\Livewire\Admin;

use App\Models\Reservation;
use App\Services\RefundService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class DailyReservations extends Component
{
    public $selectedDate;
    public $reservationToRefund = null;
    public $refundType = null;
    public $showRefundModal = false;

    public function mount()
    {
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function confirmRefund($reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);
        $refundService = app(RefundService::class);

        $refundType = $refundService->determineRefundType($reservation);

        if ($refundType === null) {
            $this->dispatch('refund-error', message: 'No se puede reembolsar con menos de 2 horas de antelación.');
            return;
        }

        $this->reservationToRefund = $reservation;
        $this->refundType = $refundType;

        $this->dispatch('open-modal', name: 'refund-modal');
    }

    public function processRefund()
    {
        if (!$this->reservationToRefund) {
            return;
        }

        $refundService = app(RefundService::class);
        $result = $refundService->processRefund($this->reservationToRefund, $this->refundType);

        if ($result['success']) {
            $this->dispatch('refund-success', message: $result['message']);
        } else {
            $this->dispatch('refund-error', message: $result['message']);
        }

        $this->reset(['reservationToRefund', 'refundType', 'showRefundModal']);
        $this->dispatch('close-modal', name: 'refund-modal');
    }

    public function refund($reservationId)
    {
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
