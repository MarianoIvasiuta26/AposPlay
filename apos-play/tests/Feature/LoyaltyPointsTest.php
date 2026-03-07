<?php

namespace Tests\Feature;

use App\Enums\LoyaltyPointType;
use App\Enums\ReservationStatus;
use App\Models\Court;
use App\Models\CourtAddress;
use App\Models\LoyaltyPoint;
use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyPointsTest extends TestCase
{
    use RefreshDatabase;

    private function createReservation(User $user, string $status = 'pending'): Reservation
    {
        $address = CourtAddress::create([
            'street' => 'Test Street',
            'number' => '123',
            'city' => 'City',
            'province' => 'Prov',
            'zip_code' => '1234',
            'country' => 'Country',
        ]);

        $court = Court::create([
            'name' => 'Test Court',
            'price' => 100,
            'type' => 'football',
            'court_address_id' => $address->id,
            'number_players' => 10,
        ]);

        $schedule = Schedule::create([
            'day_of_week' => 1,
            'start_time' => '14:00:00',
            'end_time' => '15:00:00',
            'turn' => 'afternoon',
            'is_available' => true,
        ]);

        return Reservation::create([
            'user_id' => $user->id,
            'court_id' => $court->id,
            'schedule_id' => $schedule->id,
            'reservation_date' => now()->addDays(2),
            'start_time' => '14:00:00',
            'duration_hours' => 1,
            'status' => $status,
            'total_price' => 100,
        ]);
    }

    public function test_points_earned_on_paid_reservation(): void
    {
        $user = User::factory()->create();
        $reservation = $this->createReservation($user, ReservationStatus::PENDING->value);

        $reservation->status = ReservationStatus::PAID;
        $reservation->save();

        $this->assertDatabaseHas('loyalty_points', [
            'user_id' => $user->id,
            'reservation_id' => $reservation->id,
            'type' => LoyaltyPointType::EARNED->value,
            'points' => config('loyalty.points_per_reservation'),
        ]);
    }

    public function test_points_reversed_on_cancelled_reservation(): void
    {
        $user = User::factory()->create();
        $reservation = $this->createReservation($user, ReservationStatus::PENDING->value);

        // First earn points
        $reservation->status = ReservationStatus::PAID;
        $reservation->save();

        // Then cancel
        $reservation->status = ReservationStatus::CANCELLED;
        $reservation->save();

        $this->assertDatabaseHas('loyalty_points', [
            'user_id' => $user->id,
            'reservation_id' => $reservation->id,
            'type' => LoyaltyPointType::REVERSED->value,
        ]);

        $loyaltyService = app(LoyaltyService::class);
        $this->assertEquals(0, $loyaltyService->getBalance($user));
    }

    public function test_no_duplicate_points_on_same_reservation(): void
    {
        $user = User::factory()->create();
        $reservation = $this->createReservation($user, ReservationStatus::PENDING->value);

        $reservation->status = ReservationStatus::PAID;
        $reservation->save();

        // Try to earn again by changing another field and back
        $reservation->notes = 'updated';
        $reservation->save();

        $earnedCount = LoyaltyPoint::where('reservation_id', $reservation->id)
            ->where('type', LoyaltyPointType::EARNED)
            ->count();

        $this->assertEquals(1, $earnedCount);
    }

    public function test_balance_calculation(): void
    {
        $user = User::factory()->create();

        // Create two reservations and pay them
        $reservation1 = $this->createReservation($user, ReservationStatus::PENDING->value);
        $reservation1->status = ReservationStatus::PAID;
        $reservation1->save();

        $reservation2 = $this->createReservation($user, ReservationStatus::PENDING->value);
        $reservation2->status = ReservationStatus::PAID;
        $reservation2->save();

        $loyaltyService = app(LoyaltyService::class);
        $expectedPoints = config('loyalty.points_per_reservation') * 2;
        $this->assertEquals($expectedPoints, $loyaltyService->getBalance($user));

        // Cancel one
        $reservation1->status = ReservationStatus::CANCELLED;
        $reservation1->save();

        $this->assertEquals(config('loyalty.points_per_reservation'), $loyaltyService->getBalance($user));
    }
}
