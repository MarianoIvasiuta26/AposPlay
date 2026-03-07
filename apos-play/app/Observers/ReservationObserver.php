<?php

namespace App\Observers;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Services\LoyaltyService;

class ReservationObserver
{
    public function __construct(
        protected LoyaltyService $loyaltyService
    ) {}

    public function updated(Reservation $reservation): void
    {
        if (!$reservation->wasChanged('status')) {
            return;
        }

        if ($reservation->status === ReservationStatus::PAID) {
            $this->loyaltyService->earnPoints($reservation);
        }

        if ($reservation->status === ReservationStatus::CANCELLED) {
            $this->loyaltyService->reversePoints($reservation);
        }
    }
}
