<?php

namespace App\Livewire\Staff;

use App\Enums\ReservationStatus;
use App\Models\Court;
use App\Models\Reservation;
use App\Services\RefundService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Reservations extends Component
{
    public string $selectedDate = '';

    // Refund modal
    public ?int $reservationToRefundId = null;
    public ?string $refundType = null;
    public bool $showRefundModal = false;

    public function mount()
    {
        $this->selectedDate = Carbon::today('America/Argentina/Buenos_Aires')->toDateString();
    }

    public function confirmReservation(int $reservationId)
    {
        $reservation = $this->findScopedReservation($reservationId);
        if (!$reservation) return;

        $reservation->update(['status' => ReservationStatus::CONFIRMED]);
        session()->flash('success', 'Reserva confirmada exitosamente.');
    }

    public function confirmRefund(int $reservationId)
    {
        $reservation = $this->findScopedReservation($reservationId);
        if (!$reservation) return;

        $refundService = app(RefundService::class);
        $refundType = $refundService->determineRefundType($reservation);

        if ($refundType === null) {
            session()->flash('error', 'No se puede reembolsar con menos de 2 horas de antelacion.');
            return;
        }

        $this->reservationToRefundId = $reservation->id;
        $this->refundType = $refundType;
        $this->showRefundModal = true;
    }

    public function processRefund()
    {
        if (!$this->reservationToRefundId) return;

        $reservation = $this->findScopedReservation($this->reservationToRefundId);
        if (!$reservation) return;

        $refundService = app(RefundService::class);
        $result = $refundService->processRefund($reservation, $this->refundType);

        if ($result['success']) {
            session()->flash('success', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }

        $this->reset(['reservationToRefundId', 'refundType', 'showRefundModal']);
    }

    public function cancelRefund()
    {
        $this->reset(['reservationToRefundId', 'refundType', 'showRefundModal']);
    }

    private function findScopedReservation(int $reservationId): ?Reservation
    {
        $courtIds = $this->getScopedCourtIds();

        $reservation = Reservation::where('id', $reservationId);

        if ($courtIds !== null) {
            $reservation->whereIn('court_id', $courtIds);
        }

        return $reservation->first();
    }

    private function getScopedCourtIds()
    {
        $user = auth()->user();

        if ($user->isSuperadmin()) {
            return null;
        }

        if ($user->isStaff()) {
            // Canchas via complejo asignado
            $complexes  = $user->complexesStaff()->with('owner')->get();
            $complexIds = $complexes->pluck('id');
            $viaComplex = Court::whereIn('complex_id', $complexIds)->pluck('id');

            // Canchas directas de los owners de esos complejos
            $ownerIds  = $complexes->pluck('owner_id')->unique();
            $viaDirect = \App\Models\CourtsXAdmin::whereIn('user_id', $ownerIds)->pluck('court_id');

            return $viaComplex->merge($viaDirect)->unique()->values();
        }

        // Owner: canchas via complejo + canchas asociadas directamente via courts_x_admins
        $complexIds = $user->complexesOwned()->pluck('id');
        $viaComplex = Court::whereIn('complex_id', $complexIds)->pluck('id');
        $viaDirect  = \App\Models\CourtsXAdmin::where('user_id', $user->id)->pluck('court_id');

        return $viaComplex->merge($viaDirect)->unique()->values();
    }

    public function render()
    {
        $courtIds = $this->getScopedCourtIds();

        $query = Reservation::with(['user', 'court.address'])
            ->whereDate('reservation_date', $this->selectedDate);

        if ($courtIds !== null) {
            $query->whereIn('court_id', $courtIds);
        }

        $reservations = $query->orderBy('start_time')->get();

        return view('livewire.staff.reservations', [
            'reservations' => $reservations,
        ]);
    }
}
