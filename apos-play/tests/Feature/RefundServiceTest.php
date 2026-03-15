<?php

namespace Tests\Feature;

use App\Enums\ReservationStatus;
use App\Models\Court;
use App\Models\CourtAddress;
use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\User;
use App\Services\RefundService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class RefundServiceTest extends TestCase
{
    use RefreshDatabase;

    private function createPaidReservation(User $user, Carbon $reservationStart): Reservation
    {
        $address = CourtAddress::create([
            'street' => 'Test Street', 'number' => '123',
            'city' => 'City', 'province' => 'Prov',
            'zip_code' => '1234', 'country' => 'Country',
        ]);

        $court = Court::create([
            'name' => 'Test Court', 'price' => 100,
            'type' => 'football', 'court_address_id' => $address->id,
            'number_players' => 10,
        ]);

        $schedule = Schedule::create([
            'day_of_week' => 1, 'start_time' => '14:00:00',
            'end_time' => '15:00:00', 'turn' => 'afternoon',
            'is_available' => true,
        ]);

        return Reservation::create([
            'user_id' => $user->id,
            'court_id' => $court->id,
            'schedule_id' => $schedule->id,
            'reservation_date' => $reservationStart->toDateString(),
            'start_time' => $reservationStart->format('H:i:s'),
            'duration_hours' => 1,
            'status' => ReservationStatus::PAID->value,
            'total_price' => 1000,
            'amount_paid' => 1000,
            'payment_id' => 'test_payment_123',
            'payment_status' => 'paid',
        ]);
    }

    public function test_full_refund_processes_correctly(): void
    {
        $user = User::factory()->create();
        $reservation = $this->createPaidReservation($user, now()->addHours(10));

        $service = Mockery::mock(RefundService::class)->makePartial();
        $service->shouldReceive('refundViaMercadoPago')->once();

        $result = $service->processRefund($reservation, 'full');

        $this->assertTrue($result['success']);
        $this->assertEquals(1000, $result['amount']);
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => ReservationStatus::CANCELLED->value,
            'payment_status' => 'refunded',
        ]);
    }

    public function test_partial_refund_processes_correctly(): void
    {
        $user = User::factory()->create();
        $reservation = $this->createPaidReservation($user, now()->addHours(5));

        $service = Mockery::mock(RefundService::class)->makePartial();
        $service->shouldReceive('refundViaMercadoPago')->once();

        $result = $service->processRefund($reservation, 'partial');

        $this->assertTrue($result['success']);
        $this->assertEquals(500, $result['amount']);
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => ReservationStatus::CANCELLED->value,
            'payment_status' => 'partial_refunded',
        ]);
    }

    public function test_refund_not_allowed_without_payment_id(): void
    {
        $user = User::factory()->create();
        $reservation = $this->createPaidReservation($user, now()->addHours(10));
        $reservation->update(['payment_id' => null]);

        $service = new RefundService();
        $result = $service->processRefund($reservation, 'full');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('no tiene un pago asociado', $result['message']);
    }

    public function test_determine_refund_type_full_over_8_hours(): void
    {
        $user = User::factory()->create();
        $reservation = $this->createPaidReservation($user, now()->addHours(10));

        $service = new RefundService();
        $this->assertEquals('full', $service->determineRefundType($reservation));
    }

    public function test_determine_refund_type_partial_between_2_and_8(): void
    {
        $user = User::factory()->create();
        $reservation = $this->createPaidReservation($user, now()->addHours(5));

        $service = new RefundService();
        $this->assertEquals('partial', $service->determineRefundType($reservation));
    }

    public function test_determine_refund_type_null_under_2_hours(): void
    {
        $user = User::factory()->create();
        $reservation = $this->createPaidReservation($user, now()->addMinutes(90));

        $service = new RefundService();
        $this->assertNull($service->determineRefundType($reservation));
    }
}
