<?php

namespace Tests\Feature;

use App\Enums\ReservationStatus;
use App\Models\Complex;
use App\Models\Court;
use App\Models\CourtAddress;
use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\User;
use App\Notifications\CancellationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CancellationNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function createReservationWithComplex(User $user, ?Complex $complex = null): Reservation
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
            'complex_id' => $complex?->id,
        ]);

        $schedule = Schedule::create([
            'day_of_week' => 1, 'start_time' => '14:00:00',
            'end_time' => '15:00:00', 'turn' => 'afternoon',
            'is_available' => true,
        ]);

        return Reservation::create([
            'user_id' => $user->id, 'court_id' => $court->id,
            'schedule_id' => $schedule->id,
            'reservation_date' => now()->addDays(2),
            'start_time' => '14:00:00', 'duration_hours' => 1,
            'status' => ReservationStatus::PAID->value,
            'total_price' => 100,
        ]);
    }

    public function test_staff_notified_on_reservation_cancellation(): void
    {
        Notification::fake();

        $owner = User::factory()->owner()->create();
        $staff = User::factory()->staff()->create();

        $complex = Complex::create([
            'name' => 'Test Complex', 'owner_id' => $owner->id,
            'address' => '123 Test St', 'active' => true,
        ]);
        $complex->staff()->attach($staff->id);

        $user = User::factory()->create();
        $reservation = $this->createReservationWithComplex($user, $complex);

        $reservation->status = ReservationStatus::CANCELLED;
        $reservation->save();

        Notification::assertSentTo($staff, CancellationNotification::class);
    }

    public function test_no_notification_if_court_has_no_complex(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $reservation = $this->createReservationWithComplex($user, null);

        $reservation->status = ReservationStatus::CANCELLED;
        $reservation->save();

        Notification::assertNothingSent();
    }
}
