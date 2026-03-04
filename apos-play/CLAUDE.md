# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**AposPlay** is a sports court reservation platform for Argentina. Users can browse courts (canchas), check availability for the next 7 days, book hourly slots, and pay via Mercado Pago. Admins can view daily reservations and process refunds.

Stack: Laravel 12 + Livewire Flux v2 + Volt + Tailwind CSS v4 + MySQL (via Laravel Sail/Docker) + Mercado Pago.

## Development Commands

All dev services run concurrently (server, queue, logs, Vite):
```bash
composer run dev
```

Run tests (clears config first):
```bash
composer test
# or run a specific test file:
php artisan test tests/Feature/MyReservationsTest.php
# or with Pest directly:
./vendor/bin/pest --filter="test name"
```

Fresh install using Docker:
```bash
bash scripts/install.sh
```

Manual Sail workflow:
```bash
vendor/bin/sail up -d
vendor/bin/sail artisan migrate:fresh --seed
vendor/bin/sail npm install && vendor/bin/sail npm run dev
```

## Environment Variables

Beyond the standard Laravel defaults (see `.env.example`), Mercado Pago requires:
- `TEST_ACCESS_TOKEN` — MercadoPago sandbox access token (maps to `services.mercadopago.access_token`)
- `TEST_PUBLIC_KEY` — MercadoPago sandbox public key
- `MERCADOPAGO_TEST_USER_EMAIL` — Test payer email used in payment preference creation

Database defaults: MySQL via Sail (`DB_HOST=mysql`, `DB_DATABASE=laravel`, `DB_USERNAME=sail`, `DB_PASSWORD=password`).

## Architecture

### Routing & Controllers
- `routes/web.php` — All routes. Auth-gated group contains user routes (reservations, payment) and one unprotected admin route (`/admin/reservas-del-dia` — role middleware is a known TODO).
- `MercadoPagoController` — Traditional controller handling payment preference creation and MP callback redirects (success/failure/pending).

### Livewire Components
- `App\Livewire\User\CourtAvailability` — Main booking flow. Loads all courts, generates 7-day availability grids by querying `schedules`/`schedules_x_courts`, cross-referencing existing reservations. Opens a modal to confirm bookings. All availability logic runs in PHP (no separate API).
- `App\Livewire\User\MyReservations` — Shows user's reservations; handles cancel (with 24h rule) and redirects to MP payment.
- `App\Livewire\Admin\DailyReservations` — Admin view of reservations by date; handles full/partial refund flow (currently simulated — see TODOs below).
- Settings pages use Livewire Volt (`resources/views/livewire/settings/`).

### Models & Data
Core models (all use soft deletes): `Court`, `CourtAddress`, `Schedule`, `SchedulesXCourt`, `Reservation`, `CourtsXAdmin`.

**Dual data model (legacy vs. new):** There are two parallel court systems in the database:
- Old: `courts` + `schedules` + `schedules_x_courts` — used by `CourtAvailability` for booking.
- New: `canchas` + `dias` + `cancha_horarios` — added later; `Court` model has a `horarios()` relation through `cancha_horarios` pivot. Both systems coexist; the new one is not yet fully integrated into the booking flow.

`Reservation` uses:
- `status` → cast to `App\Enums\ReservationStatus` (pending, pending_payment, paid, confirmed, cancelled)
- `payment_status` → raw string field (paid, refunded, partial_refunded)
- `payment_id` → MercadoPago payment ID from callback

Timezone: All date/availability logic uses `America/Argentina/Buenos_Aires`.

### Payment Flow
1. User clicks "Pay" on a reservation → `MyReservations::pay()` redirects to `mercadopago.create`.
2. `MercadoPagoController::createPreference()` calls MP API and redirects to `$preference->init_point`.
3. MP redirects back to success/failure/pending routes; `success()` updates reservation status to `PAID`.

### Known TODOs
- Mercado Pago **refund** is simulated (logged only, no actual API call). Real implementation should use `MercadoPago\Client\Payment\PaymentClient::refund()`.
- Admin routes lack role-based middleware protection.
- `CourtAvailability::loadAvailability()` queries the DB on every availability load with many individual queries — potential N+1 issue at scale.
