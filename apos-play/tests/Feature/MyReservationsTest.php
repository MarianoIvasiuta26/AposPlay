<?php

namespace Tests\Feature;

use App\Enums\ReservationStatus;
use App\Livewire\User\MyReservations;
use App\Models\Court;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MyReservationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_my_reservations_page_is_rendered()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('my-reservations'))
            ->assertOk()
            ->assertSeeLivewire(MyReservations::class);
    }

    public function test_user_can_see_their_reservations()
    {
        $user = User::factory()->create();

        $address = \App\Models\CourtAddress::create([
            'street' => 'Test Street',
            'number' => '123',
            'city' => 'City',
            'province' => 'Prov',
            'zip_code' => '1234',
            'country' => 'Country'
        ]);

        $court = Court::create([
            'name' => 'Test Court',
            'price' => 100,
            'type' => 'football',
            'court_address_id' => $address->id,
            'number_players' => 10
        ]);

        $schedule = Schedule::create([
            'day_of_week' => 1,
            'start_time' => '14:00:00',
            'end_time' => '15:00:00',
            'turn' => 'afternoon',
            'is_available' => true
        ]);

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'court_id' => $court->id,
            'schedule_id' => $schedule->id,
            'reservation_date' => now()->addDays(2),
            'start_time' => '14:00:00',
            'duration_hours' => 1,
            'status' => ReservationStatus::CONFIRMED->value,
            'total_price' => 100
        ]);

        Livewire::actingAs($user)
            ->test(MyReservations::class)
            ->assertSee('Test Court')
            ->assertSee(now()->addDays(2)->day);
    }

    public function test_user_can_cancel_reservation_if_more_than_24_hours()
    {
        $user = User::factory()->create();

        $address = \App\Models\CourtAddress::create(['street' => 'S', 'number' => '1', 'city' => 'C', 'province' => 'P', 'zip_code' => 'Z', 'country' => 'Co']);
        $court = Court::create(['name' => 'C', 'price' => 100, 'type' => 'f', 'court_address_id' => $address->id, 'number_players' => 10]);
        $schedule = Schedule::create(['day_of_week' => 1, 'start_time' => '00:00', 'end_time' => '23:00', 'turn' => 'morning', 'is_available' => true]);

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'court_id' => $court->id,
            'schedule_id' => $schedule->id,
            'reservation_date' => now()->addHours(25)->toDateString(),
            'start_time' => now()->addHours(25)->toTimeString(),
            'duration_hours' => 1,
            'status' => ReservationStatus::CONFIRMED->value,
            'total_price' => 100
        ]);

        Livewire::actingAs($user)
            ->test(MyReservations::class)
            ->call('cancel', $reservation->id)
            ->assertDispatched('reservation-cancelled');

        $this->assertEquals(ReservationStatus::CANCELLED->value, $reservation->fresh()->status);
    }

    public function test_user_cannot_cancel_reservation_if_less_than_24_hours()
    {
        $user = User::factory()->create();

        $address = \App\Models\CourtAddress::create(['street' => 'S', 'number' => '1', 'city' => 'C', 'province' => 'P', 'zip_code' => 'Z', 'country' => 'Co']);
        $court = Court::create(['name' => 'C', 'price' => 100, 'type' => 'f', 'court_address_id' => $address->id, 'number_players' => 10]);
        $schedule = Schedule::create(['day_of_week' => 1, 'start_time' => '00:00', 'end_time' => '23:00', 'turn' => 'morning', 'is_available' => true]);

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'court_id' => $court->id,
            'schedule_id' => $schedule->id,
            'reservation_date' => now()->addHours(23)->toDateString(),
            'start_time' => now()->addHours(23)->toTimeString(),
            'duration_hours' => 1,
            'status' => ReservationStatus::CONFIRMED->value,
            'total_price' => 100
        ]);

        Livewire::actingAs($user)
            ->test(MyReservations::class)
            ->call('cancel', $reservation->id)
            ->assertDispatched('reservation-error');

        $this->assertEquals(ReservationStatus::CONFIRMED->value, $reservation->fresh()->status);
    }
}
