<?php

namespace App\Http\Controllers;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;

class MercadoPagoController extends Controller
{
    public function __construct()
    {
        $accessToken = config('services.mercadopago.access_token');
        if (empty($accessToken)) {
            Log::error('MercadoPago Access Token is missing in config.');
        } else {
            MercadoPagoConfig::setAccessToken($accessToken);
        }
    }

    public function createPreference(Reservation $reservation)
    {
        if ($reservation->status === ReservationStatus::PAID->value) {
            return redirect()->route('my-reservations')->with('error', 'Esta reserva ya está pagada.');
        }

        if (empty(config('services.mercadopago.access_token'))) {
            return redirect()->route('my-reservations')->with('error', 'Error de configuración: Falta el Token de Acceso de Mercado Pago.');
        }

        $client = new PreferenceClient();

        try {
            Log::info("Using Access Token Prefix: " . substr(config('services.mercadopago.access_token'), 0, 5) . "...");
            Log::info("Creating preference for Reservation ID: {$reservation->id}");

            $preferenceData = [
                "items" => [
                    [
                        "title" => "Reserva de Cancha - " . $reservation->court->name,
                        "quantity" => 1,
                        // Ensure unit_price is a float/integer, MercadoPago can be picky
                        "unit_price" => (float) $reservation->total_price,
                        "currency_id" => "ARS"
                    ]
                ],
                "payer" => [
                    "email" => config('services.mercadopago.test_user_email') ?? $reservation->user->email,
                ],
                "external_reference" => (string) $reservation->id,
                "back_urls" => [
                    "success" => route('mercadopago.success'),
                    "failure" => route('mercadopago.failure'),
                    "pending" => route('mercadopago.pending'),
                ],
                //"auto_return" => "approved", // Blocked by Mercado Pago on localhost with APP_USR- credentials (requires HTTPS)
            ];

            Log::info("Preference Data being sent: " . json_encode($preferenceData));

            $preference = $client->create($preferenceData);

            Log::info("Back URLs: " . json_encode([
                "success" => route('mercadopago.success'),
                "failure" => route('mercadopago.failure'),
                "pending" => route('mercadopago.pending'),
            ]));

            Log::info("Preference created. Init Point: " . $preference->init_point);
            return redirect($preference->init_point);

        } catch (MPApiException $e) {
            $response = $e->getApiResponse();
            $content = $response ? $response->getContent() : 'No response content';
            Log::error('MercadoPago API Error: ' . json_encode($content));
            return redirect()->route('my-reservations')->with('error', 'Error de Mercado Pago: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('MercadoPago Error in createPreference: ' . $e->getMessage());
            return redirect()->route('my-reservations')->with('error', 'Error al iniciar el pago con Mercado Pago: ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        // Validate payment status
        $paymentId = $request->query('payment_id');
        $status = $request->query('status');
        $externalReference = $request->query('external_reference');

        Log::info("MercadoPago Success Callback: Payment ID: $paymentId, Status: $status, Reference: $externalReference");

        if ($status === 'approved') {
            $reservation = Reservation::find($externalReference);

            if ($reservation) {
                Log::info("Updating reservation $externalReference to PAID.");
                if ($reservation->status !== ReservationStatus::PAID) {
                    $reservation->update([
                        'status' => ReservationStatus::PAID,
                        'payment_status' => 'paid',
                        'payment_id' => $paymentId,
                        'amount_paid' => $reservation->total_price
                    ]);
                    Log::info("Reservation $externalReference updated successfully.");
                } else {
                    Log::info("Reservation $externalReference was already PAID.");
                }
            } else {
                Log::error("Reservation $externalReference not found.");
            }

            return redirect()->route('my-reservations')->with('success', 'Pago realizado con éxito! Tu reserva está confirmada.');
        }

        Log::warning("Payment not approved. Status: $status");
        return redirect()->route('my-reservations')->with('error', 'El pago no fue aprobado.');
    }

    public function failure(Request $request)
    {
        return redirect()->route('my-reservations')->with('error', 'El pago fue rechazado o cancelado.');
    }

    public function pending(Request $request)
    {
        return redirect()->route('my-reservations')->with('warning', 'El pago está pendiente de confirmación.');
    }

    /**
     * Returns the MP checkout URL as JSON (used by Livewire to open in new tab).
     */
    public function preferenceUrl(Reservation $reservation)
    {
        if ($reservation->user_id !== auth()->id()) {
            abort(403);
        }

        if ($reservation->status === ReservationStatus::PAID) {
            return response()->json(['error' => 'Esta reserva ya está pagada.'], 422);
        }

        $client = new PreferenceClient();

        try {
            $preferenceData = [
                "items" => [[
                    "title"      => "Reserva de Cancha - " . $reservation->court->name,
                    "quantity"   => 1,
                    "unit_price" => (float) $reservation->total_price,
                    "currency_id" => "ARS",
                ]],
                "payer" => [
                    "email" => config('services.mercadopago.test_user_email') ?? $reservation->user->email,
                ],
                "external_reference" => (string) $reservation->id,
                "back_urls" => [
                    "success" => route('mercadopago.success'),
                    "failure" => route('mercadopago.failure'),
                    "pending" => route('mercadopago.pending'),
                ],
            ];

            $preference = $client->create($preferenceData);
            Log::info("Preference URL created for Reservation {$reservation->id}: {$preference->init_point}");

            return response()->json(['url' => $preference->init_point]);

        } catch (MPApiException $e) {
            $content = $e->getApiResponse()?->getContent() ?? 'No response';
            Log::error('MercadoPago API Error (preferenceUrl): ' . json_encode($content));
            return response()->json(['error' => 'Error de Mercado Pago: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('MercadoPago Error (preferenceUrl): ' . $e->getMessage());
            return response()->json(['error' => 'Error al iniciar el pago.'], 500);
        }
    }

    /**
     * Webhook handler: MercadoPago notifies us of payment updates.
     */
    public function webhook(Request $request)
    {
        $type   = $request->input('type') ?? $request->input('topic');
        $dataId = $request->input('data.id') ?? $request->query('id');

        Log::info("MP Webhook received: type={$type}, id={$dataId}");

        if ($type !== 'payment' || empty($dataId)) {
            return response()->json(['status' => 'ignored'], 200);
        }

        try {
            $paymentClient = new PaymentClient();
            $payment = $paymentClient->get((int) $dataId);

            $status            = $payment->status;
            $externalReference = $payment->external_reference;
            $paymentId         = $payment->id;

            Log::info("MP Webhook payment: id={$paymentId}, status={$status}, reference={$externalReference}");

            $reservation = Reservation::find($externalReference);
            if (! $reservation) {
                Log::warning("MP Webhook: Reservation {$externalReference} not found.");
                return response()->json(['status' => 'not_found'], 200);
            }

            if ($status === 'approved' && $reservation->status !== ReservationStatus::PAID) {
                $reservation->update([
                    'status'       => ReservationStatus::PAID,
                    'payment_status' => 'paid',
                    'payment_id'   => $paymentId,
                    'amount_paid'  => $reservation->total_price,
                ]);
                Log::info("MP Webhook: Reservation {$externalReference} marked as PAID.");
            }

        } catch (\Exception $e) {
            Log::error("MP Webhook error: " . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }

        return response()->json(['status' => 'ok'], 200);
    }
}
