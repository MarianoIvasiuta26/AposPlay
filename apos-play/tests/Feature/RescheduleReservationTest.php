<?php

namespace Tests\Feature;

use App\Enums\ReservationStatus;
use App\Models\Court;
use App\Models\CourtAddress;
use App\Models\CourtBlock;
use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\SchedulesXCourt;
use App\Models\User;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RescheduleReservationTest extends TestCase
{
    use RefreshDatabase;

    private Court $court;
    private Schedule $schedule;

    protected function setUp(): void
    {
        parent::setUp();

        $address = CourtAddress::create([
            'street' => 'Test Street', 'number' => '123',
            'city' => 'City', 'province' => 'Prov',
            'zip_code' => '1234', 'country' => 'Country',
        ]);

        $this->court = Court::create([
            'name' => 'Test Court', 'price' => 100,
            'type' => 'football', 'court_address_id' => $address->id,
            'number_players' => 10,
        ]);

        // Create schedules for several days
        for ($day = 0; $day <= 6; $day++) {
            $this->schedule = Schedule::create([
                'day_of_week' => $day,
                'start_time' => '08:00:00',
                'end_time' => '22:00:00',
                'turn' => 'morning',
                'is_available' => true,
            ]);

            SchedulesXCourt::create([
                'court_id' => $this->court->id,
                'schedule_id' => $this->schedule->id,
            ]);
        }
    }

    private function createReservation(User $user, string $status = 'pending', ?Carbon $date = null): Reservation
    {
        $date = $date ?? now()->addDays(3);

        return Reservation::create([
            'user_id' => $user->id,
            'court_id' => $this->court->id,
            'schedule_id' => $this->schedule->id,
            'reservation_date' => $date->toDateString(),
            'start_time' => '14:00:00',
            'duration_hours' => 1,
            'status' => $status,
            'total_price' => 100,
        ]);
    }

    public function test_user_can_reschedule_with_4h_anticipation(): void
    {
        $user = User::factory()->create();
        $reservation = $this->createReservation($user, ReservationStatus::PENDING->value);

        $newDate = now()->addDays(4)->toDateString();

        $service = app(ReservationService::class);
        $result = $service->reschedule($reservation, $newDate, '16:00', 1);

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'reservation_date' => $newDate,
            'start_time' => '16:00',
        ]);
    }

    public function test_user_cannot_reschedule_with_less_than_4h(): void
    {
        $user = User::factory()->create();

        // Create a reservation starting in 2 hours
        $reservation = Reservation::create([
            'user_id' => $user->id,
            'court_id' => $this->court->id,
            'schedule_id' => $this->schedule->id,
            'reservation_date' => now()->toDateString(),
            'start_time' => now()->addHours(2)->format('H:i:s'),
            'duration_hours' => 1,
            'status' => ReservationStatus::PENDING->value,
            'total_price' => 100,
        ]);

        $newDate = now()->addDays(4)->toDateString();

        $service = app(ReservationService::class);
        $result = $service->reschedule($reservation, $newDate, '16:00', 1);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('no puede ser reprogramada', $result['message']);
    }

    public function test_reschedule_fails_if_new_slot_unavailable(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $newDate = now()->addDays(4)->toDateString();

        // Create an existing reservation at the target slot
        Reservation::create([
            'user_id' => $otherUser->id,
            'court_id' => $this->court->id,
            'schedule_id' => $this->schedule->id,
            'reservation_date' => $newDate,
            'start_time' => '16:00:00',
            'duration_hours' => 1,
            'status' => ReservationStatus::CONFIRMED->value,
            'total_price' => 100,
        ]);

        $reservation = $this->createReservation($user, ReservationStatus::PENDING->value);

        $service = app(ReservationService::class);
        $result = $service->reschedule($reservation, $newDate, '16:00', 1);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('reservado', $result['message']);
    }

    public function test_cancelled_reservation_cannot_be_rescheduled(): void
    {
        $user = User::factory()->create();
        $reservation = $this->createReservation($user, ReservationStatus::CANCELLED->value);

        $service = app(ReservationService::class);
        $result = $service->reschedule($reservation, now()->addDays(4)->toDateString(), '16:00', 1);

        $this->assertFalse($result['success']);
    }

    public function test_reschedule_fails_if_slot_blocked(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->superadmin()->create();
        $reservation = $this->createReservation($user, ReservationStatus::PENDING->value);

        $newDate = now()->addDays(4)->toDateString();

        CourtBlock::create([
            'court_id' => $this->court->id,
            'start_date' => $newDate,
            'end_date' => $newDate,
            'start_time' => null,
            'end_time' => null,
            'reason' => 'Blocked',
            'created_by' => $admin->id,
        ]);

        $service = app(ReservationService::class);
        $result = $service->reschedule($reservation, $newDate, '16:00', 1);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('bloqueado', $result['message']);
    }

    public function test_paid_reservation_cannot_change_duration(): void
    {
        $user = User::factory()->create();
        $reservation = $this->createReservation($user, ReservationStatus::PAID->value);

        $service = app(ReservationService::class);
        $result = $service->reschedule($reservation, now()->addDays(4)->toDateString(), '16:00', 2);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('misma duracion', $result['message']);
    }
}
