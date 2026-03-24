# CLAUDE.md

This file provides guidance to Claude Code when working with this repository.

## Project Overview

**AposPlay** is a sports court reservation platform for Argentina. Users can browse courts, check availability for the next 7 days, book hourly slots, and pay via Mercado Pago. Owners/Staff manage daily reservations and process refunds. Owners can create and manage tournaments with team registration and payments.

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
- `routes/web.php` — All routes, grouped by role middleware.
- `MercadoPagoController` — Handles payment preference creation and MP callback redirects for both reservations and tournament team payments.

### Role Middleware
- `role:user` — only regular users
- `role:superadmin,owner` — admin-level management
- `role:superadmin,owner,staff` — all staff-level access
- Middleware registered in `bootstrap/app.php` via `RoleMiddleware`

### Livewire Components
**User (role: user)**
- `App\Livewire\User\CourtAvailability` — 7-day availability grid + booking modal with points redemption.
- `App\Livewire\User\MyReservations` — Reservations list; cancel (24h rule), reschedule modal, MP payment button, refund info.
- `App\Livewire\User\LoyaltyBalance` — Points balance and last 10 transactions.
- `App\Livewire\Tournaments\Index` — Public tournament listing.
- `App\Livewire\Tournaments\Show` — Tournament detail: info, teams, fixture/standings, player stats (4 tabs).
- `App\Livewire\Tournaments\Register` — 3-step registration wizard (team info → members → payment).

**Admin (role: superadmin,owner)**
- `App\Livewire\Admin\DailyReservations` — Daily reservation view with full/partial refund.
- `App\Livewire\Admin\Coupons` — Coupon management.
- `App\Livewire\Admin\OccupancyReport` — Court occupancy report.
- `App\Livewire\Admin\IncomeExport` — Income export.
- `App\Livewire\Admin\Owners\Index` / `Form` — Superadmin creates/manages owner accounts.
- `App\Livewire\Admin\CourtBlocks\Index` / `Form` — Schedule court blocks.
- `App\Livewire\Admin\AuditLog\Index` — System audit log.

**Admin Promotions (role: superadmin,owner,staff)**
- `App\Livewire\Admin\Promotions\Index` / `Form` — CRUD for promotions (combo, coupon, extra_points).

**Owner (role: superadmin,owner)**
- `App\Livewire\Owner\Complexes\Index` / `Form` — Complex management.
- `App\Livewire\Owner\Staff\Index` / `Form` — Staff account management.
- `App\Livewire\Owner\Tournaments\Index` — Owner's tournament list.
- `App\Livewire\Owner\Tournaments\Form` — Create/edit tournament.
- `App\Livewire\Owner\Tournaments\Teams` — Team approval and management.
- `App\Livewire\Owner\Tournaments\Fixture` — Fixture generation and result recording.

**Staff (role: superadmin,owner,staff)**
- `App\Livewire\Staff\Reservations` — Daily reservation view scoped to own complex; confirm reservations, initiate refunds.

**Settings (Volt)**
- `resources/views/livewire/settings/` — Profile, password, appearance, delete account.

### Models & Data
Core models (all use **soft deletes**): `Court`, `CourtAddress`, `Schedule`, `SchedulesXCourt`, `Reservation`, `CourtsXAdmin`, `Complex`, `LoyaltyPoint`, `Promotion`, `Coupon`, `Tournament`, `TournamentTeam`, `TournamentTeamMember`, `TournamentMatch`, `TournamentPlayerStat`.

**Dual court system (do not break):**
- Old: `courts` + `schedules` + `schedules_x_courts` — original booking system.
- New: `courts` + `court_schedules` (pivot with `day_id`, `start_time_1/2`, `end_time_1/2`) + `dias` table — active for newly created courts.
- All availability and slot logic checks old system first, falls back to new system.
- `dias` table: seeded by `DiasSeeder` (1=Lunes … 7=Domingo). Must be present.

**`Reservation` key fields:**
- `status` → cast to `App\Enums\ReservationStatus` (pending, pending_payment, paid, confirmed, cancelled)
- `payment_status` → raw string (paid, refunded, partial_refunded)
- `payment_id` → MercadoPago payment ID
- `schedule_id` → **nullable** (null for new-system courts, int for old-system courts)
- `points_redeemed` → int, default 0
- `discount_applied` → decimal 8,2, default 0
- `final_price` → decimal 8,2, nullable

**`User` key fields:**
- `role` → cast to `App\Enums\UserRole` (SUPERADMIN, OWNER, STAFF, USER)
- `is_active` → boolean (inactive users are blocked)
- Methods: `isSuperadmin()`, `isOwner()`, `isStaff()`, `isUser()`, `hasRole(...$roles)`
- Relations: `complexesOwned()`, `complexesStaff()`, `loyaltyPoints()`, `coupons()`, `reservations()`

**`Complex` key fields:**
- `owner_id` → FK to users
- `complex_staff` pivot: complex_id, user_id

### Timezone
All date/time operations must use `Carbon::now('America/Argentina/Buenos_Aires')` as the base. Never use `Carbon::tomorrow()->setTimezone()` which can return wrong day from UTC.

### Payment Flow — Reservations
1. User clicks "Pagar Reserva" → JS `fetch('/payment/preference-url/{id}')` opens MP URL in new tab.
2. `MercadoPagoController::preferenceUrl()` creates MP preference and returns JSON `{url}`.
3. MP callback → `success()` updates reservation to `ReservationStatus::PAID`.
4. User can click "Verificar pago" → `MyReservations::checkPaymentStatus()` polls MP API.

### Payment Flow — Tournament Teams
1. `TournamentService::createPaymentPreference()` creates MP preference with `external_reference = "tournament_team_{id}"`.
2. MP redirects to `/torneos/payment/success` → `TournamentService::markTeamPaid()`.

### Refund Flow
- `RefundService::processRefund()` calls MP API to refund; if MP API fails (sandbox), logs warning and simulates refund locally (updates `payment_status` to `refunded` or `partial_refunded`).
- `RefundService::determineRefundType()`: full refund if >8h before start, partial (50%) if 2–8h, no refund if <2h.

### Loyalty Points
- `config/loyalty.php`: `points_per_reservation=5`, `points_for_discount=50`, `discount_percentage=30`.
- `ReservationObserver` triggers `LoyaltyService::earnPoints()` on PAID, `reversePoints()` on cancelled/refunded.
- `LoyaltyService::getBalance(User)`, `canRedeem(User, int)`, `redeemPoints(User, Reservation, int)`.

### Court Scoping for Staff/Owner
`Staff\Reservations::getScopedCourtIds()`:
- Superadmin → `null` (no filter, sees all).
- Staff → courts via their assigned complexes (`complex_staff` → `complex_id`) **plus** courts directly owned by those complexes' owners (`courts_x_admins`).
- Owner → courts via owned complexes **plus** direct `courts_x_admins` associations.

### Known TODOs
- `CourtAvailability::loadAvailability()` has potential N+1 issue.
- Admin promotion routes allow `staff` role; consider restricting to `owner+superadmin` only.

### Known Failing Tests (pre-existing)
- `MyReservationsTest::test_user_can_cancel_reservation_if_more_than_24_hours` — `assertEquals(string, enum)`. Fix: `assertSame($reservation->fresh()->status, ReservationStatus::CANCELLED)`.
- `MyReservationsTest::test_user_cannot_cancel_reservation_if_less_than_24_hours` — Same issue.
- `PaymentsAndNotificationsTest::test_admin_can_refund_total_if_more_than_8_hours` — Expects `refund-error` event but component doesn't dispatch it.
- `PaymentsAndNotificationsTest::test_user_cancellation_triggers_automatic_refund` — Same enum vs string comparison.
- `ProfileUpdateTest::test_user_can_delete_their_account` — `expect($user->fresh())->toBeNull()` fails due to `SoftDeletes`. Fix: use `assertSoftDeleted()`.

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
- Inline modal errors: use `public string $modalError = ''` property displayed inside modal, not `session()->flash()`
- Timezone: always `Carbon::now('America/Argentina/Buenos_Aires')` as base

---

## Implemented Use Cases

### UC-01 al UC-17: Funcionalidades base
Reservas, canchas, horarios, pagos MP, reembolsos, reportes, cupones, bloqueos de cancha, roles de usuario. Todo implementado.

---

### UC-18: Acumular Puntos ✅ IMPLEMENTADO

**Descripción:** El sistema asigna puntos automáticamente al usuario por cada reserva pagada.

**Implementación:**
- Migración: `2026_03_07_000001_create_loyalty_points_table.php` — `loyalty_points` (id, user_id, reservation_id nullable, points, type enum, description, expires_at, timestamps)
- Enum: `App\Enums\LoyaltyPointType` (earned, spent, reversed, expired)
- Modelo: `App\Models\LoyaltyPoint` — soft deletes, `belongsTo User/Reservation`, scope `active()`
- Servicio: `App\Services\LoyaltyService::earnPoints()`, `reversePoints()`, `getBalance()`
- Observer: `App\Observers\ReservationObserver` — registrado en `AppServiceProvider`
- Vista: `App\Livewire\User\LoyaltyBalance` → `/mis-puntos`
- Tests: `tests/Feature/LoyaltyPointsTest.php`

---

### UC-19: Canjear Puntos ✅ IMPLEMENTADO

**Descripción:** El usuario puede aplicar puntos acumulados como descuento en una nueva reserva.

**Implementación:**
- Config: `config/loyalty.php` (points_per_reservation=5, points_for_discount=50, discount_percentage=30)
- Servicio: `LoyaltyService::canRedeem()`, `redeemPoints()`
- Migración: columnas `points_redeemed`, `discount_applied`, `final_price` en `reservations`
- `CourtAvailability`: propiedad `$usePoints`, balance via `LoyaltyService::getBalance()`, preview de precio con descuento en modal
- Tests: `tests/Feature/RedeemPointsTest.php`

---

### UC-20: Gestionar Promociones ✅ IMPLEMENTADO

**Descripción:** El Administrador puede crear o editar reglas de promoción (combo, cupón, puntos extra).

**Implementación:**
- Migración: `2026_03_07_000003_create_promotions_table.php` — `promotions` (id, name, type enum, discount_value, points_bonus, conditions json, starts_at, ends_at, is_active, created_by FK, timestamps, soft deletes)
- Enum: `App\Enums\PromotionType` (combo, coupon, extra_points)
- Modelo: `App\Models\Promotion` — scope `active()`, método `conflictsWith()`
- Policy: `App\Policies\PromotionPolicy` — solo owner/superadmin puede gestionar
- Servicio: `App\Services\PromotionService::validatePromotion()`, `applyToReservation()`
- Livewire: `Admin\Promotions\Index` + `Form` → `/admin/promociones`
- Tests: `tests/Feature/PromotionManagementTest.php`

---

### UC-21: Gestionar Roles (superadmin) ✅ IMPLEMENTADO

**Descripción:** El Superadmin puede crear y gestionar cuentas de owner, ver todos los owners/complejos y desactivar owners.

**Implementación:**
- Migración: `add_role_to_users_table` — columna `role` (enum UserRole) y `is_active` (boolean)
- Enum: `App\Enums\UserRole` (SUPERADMIN, OWNER, STAFF, USER)
- Servicio: `App\Services\RoleService`
- Livewire: `Admin\Owners\Index` + `Form` → `/admin/owners`
- Middleware: `App\Http\Middleware\RoleMiddleware` — registrado como alias `role`
- Tests: `tests/Feature/OwnerManagementTest.php`, `tests/Feature/RoleMiddlewareTest.php`

---

### UC-22: Gestionar Staff (owner + superadmin) ✅ IMPLEMENTADO

**Descripción:** El owner puede crear, asignar y revocar acceso de cuentas staff en sus complejos.

**Implementación:**
- Migración: `create_complexes_table` + `create_complex_staff_table` (pivot complex_id, user_id)
- Modelo: `App\Models\Complex` — `belongsTo owner`, `belongsToMany staff`
- Policy: `App\Policies\StaffPolicy`, `App\Policies\ComplexPolicy`
- Livewire: `Owner\Staff\Index` + `Form` → `/owner/staff`; `Owner\Complexes\Index` + `Form` → `/owner/complejos`
- Tests: `tests/Feature/StaffManagementTest.php`

---

### UC-23: Panel por Rol ✅ IMPLEMENTADO

**Descripción:** Cada rol accede a su sección correspondiente con datos filtrados.

**Implementación:**
- Superadmin: `/admin/*` — acceso total
- Owner: `/admin/*` + `/owner/*` — solo sus complejos y staff
- Staff: `/staff/*` — solo el complejo asignado (reservas filtradas por scoped court IDs)
- User: `/mis-reservas`, `/mis-puntos`, `/court-availability` — solo sus propios datos
- Rutas protegidas con `role:` middleware en todos los grupos

---

### UC-24: Módulo de Torneos ✅ IMPLEMENTADO

**Descripción:** Los dueños crean torneos, los usuarios registran equipos y pagan la inscripción. El sistema genera fixture y calcula standings y estadísticas de jugadores.

**Precondición:** Owner autenticado para gestionar; usuario autenticado para inscribirse.

**Secuencia normal:**
1. Owner crea torneo (nombre, deporte, formato, fechas, precio, cupo máximo)
2. Owner abre inscripción → el torneo queda en estado `registration_open`
3. Usuarios registran equipos con integrantes y pagan por MP
4. Owner genera fixture (round-robin o eliminación simple)
5. Owner registra resultados de partidos y estadísticas de jugadores
6. El sistema calcula standings y estadísticas en tiempo real

**Modelos:**
- `Tournament` — name, sport, format (round_robin/single_elimination), status, entry_fee, max_teams, court_id, owner_id, starts_at, ends_at
- `TournamentTeam` — name, captain_id, payment_status, payment_id, amount_paid
- `TournamentTeamMember` — team_id, user_id, jersey_number, position
- `TournamentMatch` — tournament_id, home_team_id, away_team_id, round, status, home_score, away_score, played_at
- `TournamentPlayerStat` — match_id, user_id, team_id, goals, assists, yellow_cards, red_cards, minutes_played

**Enums:**
- `TournamentStatus`: draft, registration_open, registration_closed, in_progress, finished, cancelled
- `TournamentFormat`: round_robin, single_elimination
- `TournamentTeamPaymentStatus`: pending, paid, refunded
- `TournamentMatchStatus`: scheduled, in_progress, finished, cancelled

**Servicio: `App\Services\TournamentService`**
- `create()`, `openRegistration()`, `startTournament()`, `finishTournament()`
- `registerTeam()`, `addMember()`, `removeMember()`
- `generateFixture()` — round-robin (algoritmo de rotación, n-1 rondas) o single elimination (bracket potencia de 2 con byes)
- `recordResult()` — registra score y estadísticas de jugadores
- `getStandings()` — PJ, PG, PE, PP, GF, GC, Pts ordenados
- `getPlayerStats()` — goles, asistencias, tarjetas por jugador
- `createPaymentPreference()` — crea preferencia MP por equipo
- `markTeamPaid()` — marca equipo como pagado vía callback

**Rutas:**
- `GET /torneos` → `Tournaments\Index` (todos los autenticados)
- `GET /torneos/{tournament}` → `Tournaments\Show` (4 tabs: info, equipos, fixture, estadísticas)
- `GET /torneos/{tournament}/inscribirse` → `Tournaments\Register` (wizard 3 pasos)
- `GET /torneos/payment/success|failure|pending` → callbacks de pago
- `GET /owner/torneos` → `Owner\Tournaments\Index`
- `GET /owner/torneos/crear` → `Owner\Tournaments\Form`
- `GET /owner/torneos/{tournament}/editar` → `Owner\Tournaments\Form`
- `GET /owner/torneos/{tournament}/equipos` → `Owner\Tournaments\Teams`
- `GET /owner/torneos/{tournament}/fixture` → `Owner\Tournaments\Fixture`

**Migraciones:**
- `2026_03_23_300001_create_tournaments_table.php`
- `2026_03_23_300002_create_tournament_teams_table.php`
- `2026_03_23_300003_create_tournament_team_members_table.php`
- `2026_03_23_300004_create_tournament_matches_table.php`
- `2026_03_23_300005_create_tournament_player_stats_table.php`

---

## Roles & Permissions Module

### Roles
- `superadmin` — Acceso total. Creado via seeder. Gestiona owners.
- `owner` — Gestiona sus propios complejos, canchas, staff y torneos.
- `staff` — Acceso solo al complejo asignado. Ve reservas filtradas.
- `user` — Usuario final. Puede reservar canchas e inscribirse en torneos.

### Data Model
- `users.role` → cast a `App\Enums\UserRole`
- `users.is_active` → boolean; owners desactivados no pueden acceder
- `complexes`: id, name, owner_id (FK users), address, active, soft deletes
- `complex_staff` pivot: complex_id, user_id

### Court Scoping (Staff & Owner)
Ver `Staff\Reservations::getScopedCourtIds()` y `CourtAvailability` para la lógica completa:
- Staff ve canchas vía `complex_staff` → `complex_id` → `courts.complex_id` **más** canchas directas de los owners de esos complejos vía `courts_x_admins`
- Owner ve canchas vía `complexes.owner_id` **más** `courts_x_admins` directas

### Policies
- `ComplexPolicy` — owner/superadmin gestiona; staff solo ve
- `CourtBlockPolicy` — owner/superadmin puede bloquear
- `PromotionPolicy` — owner/superadmin gestiona
- `StaffPolicy` — owner/superadmin crea/elimina staff
- `ReservationPolicy` — staff/owner ve su complejo; user ve las propias
