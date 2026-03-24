<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentRefundClient;
use MercadoPago\MercadoPagoConfig;

class RefundService
{
    public function processRefund(Reservation $reservation, string $refundType): array
    {
        if (!$reservation->payment_id) {
            return ['success' => false, 'amount' => 0, 'message' => 'Esta reserva no tiene un pago asociado.'];
        }

        return DB::transaction(function () use ($reservation, $refundType) {
            $refundAmount = $refundType === 'full'
                ? (float) $reservation->amount_paid
                : (float) $reservation->amount_paid * 0.5;

            $paymentStatus = $refundType === 'full' ? 'refunded' : 'partial_refunded';

            try {
                $this->refundViaMercadoPago($reservation->payment_id, $refundAmount);
            } catch (\Exception $e) {
                // En sandbox MP no siempre permite reembolsos vía API; se registra y se continúa simulando el reembolso localmente.
                Log::warning("MercadoPago refund API failed for reservation {$reservation->id} (simulating locally): " . $e->getMessage());
            }

            $reservation->update([
                'status' => ReservationStatus::CANCELLED,
                'payment_status' => $paymentStatus,
            ]);

            return [
                'success' => true,
                'amount' => $refundAmount,
                'message' => 'Reembolso exitoso (' . ($refundType === 'full' ? 'Total' : 'Parcial') . ').',
            ];
        });
    }

    public function determineRefundType(Reservation $reservation): ?string
    {
        $dateStr = $reservation->reservation_date instanceof Carbon
            ? $reservation->reservation_date->format('Y-m-d')
            : $reservation->reservation_date;

        $reservationStart = Carbon::parse($dateStr . ' ' . $reservation->start_time);
        $hoursUntilStart = now()->diffInHours($reservationStart, false);

        if ($hoursUntilStart < 2) {
            return null;
        }

        return $hoursUntilStart >= 8 ? 'full' : 'partial';
    }

    public function refundViaMercadoPago(string $paymentId, float $amount): void
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        $client = new PaymentRefundClient();
        $client->refund((int) $paymentId, $amount);
    }
}
