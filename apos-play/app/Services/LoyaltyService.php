<?php

namespace App\Services;

use App\Enums\LoyaltyPointType;
use App\Models\LoyaltyPoint;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    public function earnPoints(Reservation $reservation): void
    {
        DB::transaction(function () use ($reservation) {
            $alreadyEarned = LoyaltyPoint::where('reservation_id', $reservation->id)
                ->where('type', LoyaltyPointType::EARNED)
                ->exists();

            if ($alreadyEarned) {
                return;
            }

            LoyaltyPoint::create([
                'user_id' => $reservation->user_id,
                'reservation_id' => $reservation->id,
                'points' => config('loyalty.points_per_reservation'),
                'type' => LoyaltyPointType::EARNED,
                'description' => 'Puntos por reserva #' . $reservation->id,
            ]);
        });
    }

    public function reversePoints(Reservation $reservation): void
    {
        DB::transaction(function () use ($reservation) {
            $earnedRecord = LoyaltyPoint::where('reservation_id', $reservation->id)
                ->where('type', LoyaltyPointType::EARNED)
                ->first();

            if (!$earnedRecord) {
                return;
            }

            $alreadyReversed = LoyaltyPoint::where('reservation_id', $reservation->id)
                ->where('type', LoyaltyPointType::REVERSED)
                ->exists();

            if ($alreadyReversed) {
                return;
            }

            LoyaltyPoint::create([
                'user_id' => $reservation->user_id,
                'reservation_id' => $reservation->id,
                'points' => -$earnedRecord->points,
                'type' => LoyaltyPointType::REVERSED,
                'description' => 'Puntos revertidos por cancelación de reserva #' . $reservation->id,
            ]);
        });
    }

    public function getBalance(User $user): int
    {
        return (int) LoyaltyPoint::where('user_id', $user->id)
            ->active()
            ->sum('points');
    }

    public function canRedeem(User $user, int $pointsRequired): bool
    {
        return $this->getBalance($user) >= $pointsRequired;
    }

    public function redeemPoints(User $user, Reservation $reservation, int $points): void
    {
        DB::transaction(function () use ($user, $reservation, $points) {
            LoyaltyPoint::create([
                'user_id' => $user->id,
                'reservation_id' => $reservation->id,
                'points' => -$points,
                'type' => LoyaltyPointType::SPENT,
                'description' => 'Puntos canjeados en reserva #' . $reservation->id,
            ]);

            $subtotal = $reservation->total_price + ($reservation->discount_amount ?? 0);
            $pointsDiscount = round($subtotal * (config('loyalty.discount_percentage') / 100), 2);

            $reservation->update([
                'points_redeemed' => $points,
                'points_discount' => $pointsDiscount,
                'final_price' => max(0, $reservation->total_price - $pointsDiscount),
            ]);
        });
    }
}
