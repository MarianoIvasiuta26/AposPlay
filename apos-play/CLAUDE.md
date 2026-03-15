# CLAUDE.md

This file provides guidance to Claude Code when working with this repository.

## Project Overview

**AposPlay** is a sports court reservation platform for Argentina. Users can browse courts, check availability for the next 7 days, book hourly slots, and pay via Mercado Pago. Admins can view daily reservations and process refunds.

Stack: Laravel 12 + Livewire Flux v2 + Volt + Tailwind CSS v4 + MySQL (via Laravel Sail/Docker) + Mercado Pago.

## Development Commands

```bash
# Run all services concurrently (server, queue, logs, Vite)
composer run dev

# Run all tests
composer test

# Run a specific test file
./vendor/bin/sail php artisan test tests/Feature/MyReservationsTest.php

# Run with Pest filter
./vendor/bin/sail php vendor/bin/pest --filter="test name"

# Fresh install
bash scripts/install.sh

# Manual Sail workflow
vendor/bin/sail up -d
vendor/bin/sail artisan migrate:fresh --seed
vendor/bin/sail npm install && vendor/bin/sail npm run dev
```

## Environment Variables

- `TEST_ACCESS_TOKEN` — MercadoPago sandbox access token
- `TEST_PUBLIC_KEY` — MercadoPago sandbox public key
- `MERCADOPAGO_TEST_USER_EMAIL` — Test payer email
- DB: MySQL via Sail (`DB_HOST=mysql`, `DB_DATABASE=laravel`, `DB_USERNAME=sail`, `DB_PASSWORD=password`)

## Architecture

### Routing & Controllers
- `routes/web.php` — All routes. Auth-gated group for user routes; admin route `/admin/reservas-del-dia` lacks role middleware (known TODO).
- `MercadoPagoController` — Handles payment preference creation and MP callback redirects.

### Livewire Components
- `App\Livewire\User\CourtAvailability` — Main booking flow with 7-day availability grid.
- `App\Livewire\User\MyReservations` — User reservations; handles cancel (24h rule) and MP payment redirect.
- `App\Livewire\Admin\DailyReservations` — Admin view; handles full/partial refund (simulated).
- Settings pages use Livewire Volt (`resources/views/livewire/settings/`).

### Models & Data
Core models (all use **soft deletes**): `Court`, `CourtAddress`, `Schedule`, `SchedulesXCourt`, `Reservation`, `CourtsXAdmin`.

**Dual court system (do not break):**
- Old: `courts` + `schedules` + `schedules_x_courts` — active booking flow.
- New: `canchas` + `dias` + `cancha_horarios` — not yet integrated. Both coexist.

`Reservation` key fields:
- `status` → cast to `App\Enums\ReservationStatus` (pending, pending_payment, paid, confirmed, cancelled)
- `payment_status` → raw string (paid, refunded, partial_refunded)
- `payment_id` → MercadoPago payment ID

Timezone: `America/Argentina/Buenos_Aires`.

### Payment Flow
1. User clicks "Pay" → `MyReservations::pay()` → redirects to `mercadopago.create`
2. `MercadoPagoController::createPreference()` → MP API → redirect to `$preference->init_point`
3. MP callback → `success()` updates reservation to `ReservationStatus::PAID`

### Known TODOs
- MP refund is simulated (no real API call). Should use `PaymentClient::refund()`.
- Admin routes lack role middleware.
- `CourtAvailability::loadAvailability()` has potential N+1 issue.

### Known Failing Tests (pre-existing, not related to UC-18)
- `MyReservationsTest::test_user_can_cancel_reservation_if_more_than_24_hours` — Uses `assertEquals(string, enum)`. The `status` field is cast to `ReservationStatus` enum but the test compares against `->value` (string). Fix: use `assertSame($reservation->fresh()->status, ReservationStatus::CANCELLED)`.
- `MyReservationsTest::test_user_cannot_cancel_reservation_if_less_than_24_hours` — Same enum vs string comparison issue.
- `PaymentsAndNotificationsTest::test_admin_can_refund_total_if_more_than_8_hours` — Expects `refund-error` event to be dispatched but the component logic doesn't dispatch it in the tested scenario.
- `PaymentsAndNotificationsTest::test_user_cancellation_triggers_automatic_refund` — Same enum vs string comparison: `assertEquals('cancelled', $reservation->status)` where status is cast to enum.
- `ProfileUpdateTest::test_user_can_delete_their_account` — `expect($user->fresh())->toBeNull()` fails because `User` uses `SoftDeletes`. The soft-deleted user is still returned by `fresh()`. Fix: use `assertSoftDeleted()` or check `$user->fresh()->trashed()`.

---

## Coding Conventions (follow always)

- All business logic goes in `app/Services/` — never inside Livewire components
- Livewire components use typed properties and `#[Rule]` for validation
- `DB::transaction()` is mandatory for any operation modifying points or reservations
- Use existing `App\Enums\ReservationStatus` — do not create duplicates
- All new models must use soft deletes
- New migrations must not affect the dual court system
- Use Flux UI for all visual components
- Create seeders for test data on every new model
- After each UC: run `composer test` and confirm all tests pass before continuing

---

## Module: Loyalty & Promotions (UC-18, UC-19, UC-20)

### Business Rules
- 5 points awarded per paid reservation (configurable via `config/loyalty.php`)
- 50 points = 30% discount on a booking (configurable)
- Points are assigned automatically when `ReservationStatus::PAID`
- Points are reversed automatically when reservation is cancelled or refunded
- Promotions are Admin-only: types are combo, coupon, extra_points

### New Models
- `LoyaltyPoint` — tracks point transactions (earned / spent / reversed / expired)
- `Promotion` — stores promotion rules with JSON conditions

---

## UC-18: Acumular Puntos

**Descripción:** El sistema asigna puntos automáticamente al usuario por cada reserva pagada.
**Precondición:** El usuario debe haber pagado una reserva correctamente.
**Secuencia normal:**
1. El usuario realiza y paga una reserva
2. El sistema registra la transacción
3. El sistema asigna automáticamente puntos al usuario según la política vigente

**Postcondición:** El usuario acumula puntos en su perfil.
**Excepción paso 2:** Si la reserva es cancelada o reembolsada, los puntos se revierten.
**Rendimiento:** Menos de 1 segundo. **Frecuencia:** Varias veces al día. **Estabilidad:** Alta.

### Implementation Plan

**1. Migration: `loyalty_points` table**
- `id`, `user_id` (FK), `reservation_id` (FK nullable, references `reservations`), `points` (int), `type` (enum: earned/spent/reversed/expired), `description` (string), `expires_at` (datetime nullable), `timestamps`

**2. Model: `LoyaltyPoint`**
- Soft deletes
- Relations: `belongsTo User`, `belongsTo Reservation`
- Scope: `active()` — filters non-expired records
- Casts: `expires_at` as datetime, `type` as enum

**3. Service: `app/Services/LoyaltyService.php`**
- `earnPoints(Reservation $reservation): void` — assigns points per `config/loyalty.php`, wrapped in `DB::transaction()`
- `reversePoints(Reservation $reservation): void` — creates a `reversed` record, wrapped in `DB::transaction()`
- `getBalance(User $user): int` — sums active points for user

**4. Observer: `app/Observers/ReservationObserver.php`**
- On `status` → `ReservationStatus::PAID`: call `LoyaltyService::earnPoints()`
- On `status` → `cancelled` or `refunded`: call `LoyaltyService::reversePoints()`
- Register in `AppServiceProvider`

**5. Livewire Component: `resources/views/livewire/user/loyalty-balance.blade.php`**
- Shows current point balance using Flux UI
- Shows last 10 point transactions in a table

**6. Tests: `tests/Feature/LoyaltyPointsTest.php`**
- `test_points_earned_on_paid_reservation()`
- `test_points_reversed_on_cancelled_reservation()`

---

## UC-19: Canjear Puntos

**Descripción:** El usuario puede aplicar puntos acumulados como crédito al realizar una nueva reserva.
**Precondición:** El usuario tiene saldo de puntos suficiente.
**Secuencia normal:**
1. El usuario inicia una nueva reserva
2. El sistema ofrece la opción de aplicar puntos como descuento
3. El usuario selecciona usar puntos y confirma la reserva
4. El sistema descuenta los puntos y refleja el nuevo precio

**Postcondición:** Reserva creada y puntos descontados.
**Excepción paso 4:** Si no tiene puntos suficientes, se informa y continúa sin descuento.
**Rendimiento:** Menos de 1 segundo. **Frecuencia:** Pocas veces al mes por usuario. **Estabilidad:** Media.

### Implementation Plan

**1. Config: `config/loyalty.php`**
```php
return [
    'points_per_reservation' => 5,
    'points_for_discount'    => 50,
    'discount_percentage'    => 30,
];
```

**2. Add to `LoyaltyService`**
- `canRedeem(User $user, int $pointsRequired): bool`
- `redeemPoints(User $user, Reservation $reservation, int $points): void` — creates `spent` record, updates `reservation->discount_applied` and `reservation->final_price`, wrapped in `DB::transaction()`

**3. Migration: add columns to `reservations`**
- `points_redeemed` (int, default 0)
- `discount_applied` (decimal 8,2, default 0)
- `final_price` (decimal 8,2, nullable)

**4. Modify Livewire `CourtAvailability` booking modal**
- Add property: `$usePoints = false`
- Add computed: `$userBalance` via `LoyaltyService::getBalance()`
- When `$usePoints = true`: show discounted price preview
- If balance insufficient: disable option with inline message
- On confirm: call `LoyaltyService::redeemPoints()` inside `DB::transaction()`

**5. Tests: `tests/Feature/RedeemPointsTest.php`**
- `test_user_can_redeem_points_on_reservation()`
- `test_user_cannot_redeem_without_sufficient_balance()`
- `test_price_is_correctly_discounted()`

---

## UC-20: Gestionar Promociones

**Descripción:** El Administrador puede crear o editar reglas de promoción como combos, cupones o puntos extra.
**Precondición:** El usuario está autenticado como Administrador.
**Secuencia normal:**
1. El administrador accede a la sección "Promociones"
2. El sistema muestra promociones vigentes y permite crear nuevas
3. El administrador completa un formulario: tipo, duración, condiciones
4. El sistema guarda la promoción y la aplica automáticamente cuando corresponda

**Postcondición:** Nueva promoción registrada y activa.
**Excepción paso 4:** Si la promoción es inválida o se superpone con otra, el sistema lo notifica.
**Rendimiento:** Menos de 2 segundos. **Frecuencia:** Pocas veces al mes. **Estabilidad:** Media.

### Implementation Plan

**1. Migration: `promotions` table**
- `id`, `name`, `type` (enum: combo/coupon/extra_points), `discount_value` (decimal), `points_bonus` (int nullable), `conditions` (json), `starts_at` (datetime), `ends_at` (datetime), `is_active` (boolean, default true), `created_by` (FK users), `timestamps`, soft deletes

**2. Model: `Promotion`**
- Soft deletes
- Casts: `conditions` as array, `starts_at`/`ends_at` as datetime, `type` as enum
- Scope: `active()` — `is_active = true AND starts_at <= now() AND ends_at >= now()`
- Method: `conflictsWith(Promotion $other): bool`

**3. Policy: `PromotionPolicy`**
- Only users with `role = 'admin'` (consistent with existing `CourtsXAdmin` pattern) can: `viewAny`, `create`, `update`, `delete`

**4. Service: `app/Services/PromotionService.php`**
- `validatePromotion(array $data): void` — throws exception if overlapping active promotion exists
- `applyToReservation(Reservation $reservation): ?Promotion` — finds applicable active promotion

**5. Livewire: `App\Livewire\Admin\Promotions\Index`**
- View: `resources/views/livewire/admin/promotions/index.blade.php`
- Flux Table with columns: name, type, dates, status badge (active/inactive)
- Actions: create, edit, toggle active/inactive

**6. Livewire: `App\Livewire\Admin\Promotions\Form`**
- View: `resources/views/livewire/admin/promotions/form.blade.php`
- Fields: name, type, discount_value, points_bonus, starts_at, ends_at, conditions
- Real-time validation with `#[Rule]`
- On save: calls `PromotionService::validatePromotion()`
- On conflict: shows Flux alert with conflict details
- Uses `Gate::authorize('create', Promotion::class)`

**7. Routes: add to `routes/web.php` under admin group**
- `GET /admin/promociones` → `Admin\Promotions\Index`
- `GET /admin/promociones/crear` → `Admin\Promotions\Form`
- `GET /admin/promociones/{promotion}/editar` → `Admin\Promotions\Form`

**8. Tests: `tests/Feature/PromotionManagementTest.php`**
- `test_admin_can_create_promotion()`
- `test_overlapping_promotions_are_rejected()`
- `test_non_admin_cannot_manage_promotions()`

---

## Implementation Order & Session Prompts

### How to start each Claude Code session

Paste this at the beginning of every session:

```
Read CLAUDE.md fully before doing anything.

We are implementing the Loyalty & Promotions module. 
The three use cases are already fully specified in CLAUDE.md (UC-18, UC-19, UC-20).

SESSION RULES:
- Follow all conventions in the "Coding Conventions" section
- Do not modify anything related to the dual court system
- Use composer test to validate after each UC

START WITH UC-18:
1. List every file you will CREATE and every file you will MODIFY
2. Wait for my approval
3. Implement everything for UC-18 (migration + model + service + observer + livewire + test)
4. Run: vendor/bin/sail artisan migrate && composer test
5. Show me the full test output
6. Only ask to continue to UC-19 once all tests pass
```

### UC order (respect dependencies)
1. **UC-18** — creates LoyaltyPoint, LoyaltyService, ReservationObserver
2. **UC-19** — extends LoyaltyService, modifies CourtAvailability modal (depends on UC-18)
3. **UC-20** — creates Promotion, PromotionService, admin views (independent but last)

## Roles & Permissions Module

### Roles
- `superadmin` — Full system access. Created manually via seeder. Creates owner accounts.
- `owner` — Manages their own complexes and staff. Can have multiple complexes.
- `staff` — Access scoped to assigned complex only. Cannot manage other complexes.
- `user` — End user. Can browse courts and make reservations.

### Data Model
- `users.role` → enum: superadmin, owner, staff, user
- `complexes` table: id, name, owner_id (FK users), address, active, timestamps, soft deletes
- `complex_staff` pivot: complex_id, user_id, created_at

### Key Constraints
- Staff access is always scoped to their complex via complex_staff pivot
- Owner can only manage complexes where complexes.owner_id = auth()->id()
- Superadmin bypasses all scoping
- courts table must add complex_id FK (migration, not breaking change)
- Use Laravel Policies for all authorization checks
- Use middleware role:owner,staff etc. on route groups

### Policies to Create
- ComplexPolicy: owner/superadmin can manage, staff can view
- CourtPolicy: scoped to complex ownership
- StaffPolicy: owner and superadmin can create/delete staff
- ReservationPolicy: staff/owner see their complex, user sees their own

### UC-21: Gestionar Roles (superadmin)
- Superadmin puede crear cuenta de owner (nombre, email, password, asignar complejo)
- Superadmin puede ver todos los owners y sus complejos
- Superadmin puede desactivar un owner (bloquea acceso sin borrar datos)

### UC-22: Gestionar Staff (owner + superadmin)
- Owner puede crear cuentas staff para sus complejos
- Owner puede asignar staff a uno o varios de sus complejos
- Owner puede revocar acceso de staff
- Staff ve solo el complejo al que está asignado

### UC-23: Panel por Rol
- Superadmin: /admin/* — ve todo
- Owner: /owner/* — ve solo sus complejos
- Staff: /staff/* — ve solo su complejo asignado
- User: /reservas/* — ve solo sus reservas
```