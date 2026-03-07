<?php

namespace App\Services;

use App\Models\Promotion;
use App\Models\Reservation;

class PromotionService
{
    public function validatePromotion(array $data, ?int $excludeId = null): ?Promotion
    {
        $query = Promotion::where('type', $data['type'])
            ->where('is_active', true)
            ->where('starts_at', '<=', $data['ends_at'])
            ->where('ends_at', '>=', $data['starts_at']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->first();
    }

    public function applyToReservation(Reservation $reservation): ?Promotion
    {
        return Promotion::active()
            ->orderByDesc('discount_value')
            ->first();
    }
}
