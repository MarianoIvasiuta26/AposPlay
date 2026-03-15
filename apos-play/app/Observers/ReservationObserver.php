<?php

namespace App\Observers;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Notifications\CancellationNotification;
use App\Services\LoyaltyService;
use Illuminate\Support\Facades\Notification;

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
            $this->notifyStaffOfCancellation($reservation);
        }
    }

    private function notifyStaffOfCancellation(Reservation $reservation): void
    {
        $complex = $reservation->court?->complex;

        if (!$complex) {
            return;
        }

        $staffMembers = $complex->staff;

        if ($staffMembers->isNotEmpty()) {
            Notification::send($staffMembers, new CancellationNotification($reservation));
        }
    }
}
