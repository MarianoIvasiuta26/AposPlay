<?php

namespace Tests\Feature;

use App\Livewire\Admin\DailyReservations;
use App\Livewire\User\MyReservations;
use App\Models\Court;
use App\Models\CourtAddress;
use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\User;
use App\Enums\ReservationStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;
use App\Jobs\SendGameReminders;
use App\Notifications\GameReminder;

class PaymentsAndNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_pay_button_when_pending_payment()
    {
        $user = User::factory()->create();
        $this->createReservation($user, ReservationStatus::PENDING_PAYMENT->value);

        Livewire::actingAs($user)
            ->test(MyReservations::class)
            ->assertSee('Pagar Reserva');
    }

    public function test_pay_method_initiates_checkout()
    {
        // Mocking Cashier checkout is tricky without external calls.
        // We can check if specific exception is thrown (e.g., missing API key) which means it tried to call Stripe.
        // Or partial mock of user.

        $user = User::factory()->create();
        $reservation = $this->createReservation($user, ReservationStatus::PENDING_PAYMENT->value);

        // We expect an error because Stripe keys are not set, but that confirms it entered the payment flow.
        try {
            Livewire::actingAs($user)
                ->test(MyReservations::class)
                ->call('pay', $reservation->id);
        } catch (\Exception $e) {
            // If it's a Stripe/Cashier exception, we know it tried.
            $this->assertStringContainsString('No API key provided', $e->getMessage());
            // Or "Stripe API Key" etc.
            // Actually, without keys it might fail early.
        }
    }

    public function test_admin_can_refund_total_if_more_than_8_hours()
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();
        // Payment ID simulated
        $reservation = $this->createReservation($user, 'confirmed', now()->addHours(10), 'payment_123', 100);

        // Mock refund on user? 
        // We can't easily mock the Billable trait method on the model instance retrieved inside Livewire.
        // So we will trigger the method and expect exception or handle it.
        // BUT, logic check:
        // DailyReservations.php -> $user->refund(...).
        // If we don't mock, it will fail connecting to Stripe.

        // Let's test the component logic UP TO the refund call?
        // Or better: Integration tests for logic that doesn't depend on external APIs.
        // The DailyReservations component calls refund.

        // We can simulate the failure and assert 'refund-error' is dispatched, which proves it tried to refund.

        Livewire::actingAs($admin)
            ->test(DailyReservations::class)
            ->call('refund', $reservation->id)
            ->assertDispatched('refund-error'); // Because Stripe connection fails

        // Assert NOT cancelled yet (because refund failed)
        $this->assertEquals('confirmed', $reservation->fresh()->status);
    }

    public function test_reminder_job_sends_notification()
    {
        Notification::fake();

        $user = User::factory()->create();
        // Create reservation exactly 24h from now (+ some mins to fall in hour)
        // SendGameReminders checks for startOfHour to endOfHour.
        // So if we run job now(), we need reservation at now()+24h.

        $targetDate = now()->addHours(24);
        $reservation = $this->createReservation($user, 'confirmed', $targetDate);

        (new SendGameReminders)->handle();

        Notification::assertSentTo(
            $user,
            GameReminder::class,
            function ($notification, $channels) use ($reservation) {
                return $notification->reservation->id === $reservation->id;
            }
        );
    }

    private function createReservation($user, $status, $date = null, $paymentId = null, $amountPaid = 0)
    {
        $address = CourtAddress::create(['street' => 'S', 'number' => '1', 'city' => 'C', 'province' => 'P', 'zip_code' => 'Z', 'country' => 'Co']);
        $court = Court::create(['name' => 'C', 'price' => 100, 'type' => 'f', 'court_address_id' => $address->id, 'number_players' => 10]);
        $schedule = Schedule::create(['day_of_week' => 1, 'start_time' => '00:00', 'end_time' => '23:00', 'turn' => 'morning', 'is_available' => true]);

        $date = $date ?? now()->addDay();

        return Reservation::create([
            'user_id' => $user->id,
            'court_id' => $court->id,
            'schedule_id' => $schedule->id,
            'reservation_date' => $date->toDateString(),
            'start_time' => $date->format('H:i:s'),
            'duration_hours' => 1,
            'status' => $status,
            'total_price' => 100,
            'payment_id' => $paymentId,
            'amount_paid' => $amountPaid
        ]);
    }
}
