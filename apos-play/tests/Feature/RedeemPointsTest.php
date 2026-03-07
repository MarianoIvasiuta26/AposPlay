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

class RedeemPointsTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithPoints(int $points): User
    {
        $user = User::factory()->create();

        LoyaltyPoint::create([
            'user_id' => $user->id,
            'reservation_id' => null,
            'points' => $points,
            'type' => LoyaltyPointType::EARNED,
            'description' => 'Puntos de prueba',
        ]);

        return $user;
    }

    private function createReservation(User $user): Reservation
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
            'price' => 1000,
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
            'status' => ReservationStatus::PENDING->value,
            'total_price' => 1000,
        ]);
    }

    public function test_user_can_redeem_points_on_reservation(): void
    {
        $pointsRequired = config('loyalty.points_for_discount');
        $user = $this->createUserWithPoints($pointsRequired);
        $reservation = $this->createReservation($user);

        $loyaltyService = app(LoyaltyService::class);
        $loyaltyService->redeemPoints($user, $reservation, $pointsRequired);

        $this->assertDatabaseHas('loyalty_points', [
            'user_id' => $user->id,
            'reservation_id' => $reservation->id,
            'type' => LoyaltyPointType::SPENT->value,
            'points' => -$pointsRequired,
        ]);

        $reservation->refresh();
        $this->assertEquals($pointsRequired, $reservation->points_redeemed);
        $this->assertGreaterThan(0, $reservation->points_discount);
        $this->assertNotNull($reservation->final_price);
    }

    public function test_user_cannot_redeem_without_sufficient_balance(): void
    {
        $user = User::factory()->create();
        $pointsRequired = config('loyalty.points_for_discount');

        $loyaltyService = app(LoyaltyService::class);

        $this->assertFalse($loyaltyService->canRedeem($user, $pointsRequired));
        $this->assertEquals(0, $loyaltyService->getBalance($user));
    }

    public function test_price_is_correctly_discounted(): void
    {
        $pointsRequired = config('loyalty.points_for_discount');
        $discountPct = config('loyalty.discount_percentage');
        $user = $this->createUserWithPoints($pointsRequired);
        $reservation = $this->createReservation($user);

        $originalPrice = (float) $reservation->total_price;
        $expectedDiscount = round($originalPrice * ($discountPct / 100), 2);
        $expectedFinal = max(0, $originalPrice - $expectedDiscount);

        $loyaltyService = app(LoyaltyService::class);
        $loyaltyService->redeemPoints($user, $reservation, $pointsRequired);

        $reservation->refresh();

        $this->assertEquals($expectedDiscount, (float) $reservation->points_discount);
        $this->assertEquals($expectedFinal, (float) $reservation->final_price);
        $this->assertEquals(0, $loyaltyService->getBalance($user));
    }
}
