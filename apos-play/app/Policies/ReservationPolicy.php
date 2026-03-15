<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Court;
use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Reservation $reservation): bool
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        if ($user->isOwner()) {
            $courtIds = Court::whereIn('complex_id', $user->complexesOwned()->pluck('id'))->pluck('id');
            return $courtIds->contains($reservation->court_id);
        }

        if ($user->isStaff()) {
            $courtIds = Court::whereIn('complex_id', $user->complexesStaff()->pluck('complexes.id'))->pluck('id');
            return $courtIds->contains($reservation->court_id);
        }

        return $reservation->user_id === $user->id;
    }

    public function cancel(User $user, Reservation $reservation): bool
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        if ($user->isOwner()) {
            $courtIds = Court::whereIn('complex_id', $user->complexesOwned()->pluck('id'))->pluck('id');
            return $courtIds->contains($reservation->court_id);
        }

        return $reservation->user_id === $user->id;
    }
}
