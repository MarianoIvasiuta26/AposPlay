<?php

namespace App\Jobs;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Notifications\GameReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendGameReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting SendGameReminders job');

        // Check for 24 hours and 1 hour before the game
        // Checks executed hourly

        $start24 = now()->addHours(24)->startOfHour();
        $end24 = now()->addHours(24)->endOfHour();

        $start1 = now()->addHour()->startOfHour();
        $end1 = now()->addHour()->endOfHour();

        Log::info("24h window: {$start24->toDateTimeString()} to {$end24->toDateTimeString()}");
        Log::info("1h window: {$start1->toDateTimeString()} to {$end1->toDateTimeString()}");

        $sent24h = $this->processReminders($start24, $end24, '24 horas');
        $sent1h = $this->processReminders($start1, $end1, '1 hora');

        Log::info("SendGameReminders completed - 24h: {$sent24h}, 1h: {$sent1h}");
    }

    protected function processReminders($start, $end, $context): int
    {
        $targetDate = $start->toDateString();
        $targetHour = $start->format('H'); // 14

        $reservations = Reservation::where('status', ReservationStatus::CONFIRMED->value)
            ->whereDate('reservation_date', $targetDate)
            ->whereRaw("HOUR(start_time) = ?", [$targetHour])
            ->whereDoesntHave('user.notifications', function ($query) use ($context) {
                // Evitar enviar notificaciones duplicadas
                $query->where('type', GameReminder::class)
                    ->where('created_at', '>=', now()->subHours(2));
            })
            ->with(['user', 'court'])
            ->get();

        $count = 0;

        foreach ($reservations as $reservation) {
            try {
                $reservation->user->notify(new GameReminder($reservation, $context));
                Log::info("Reminder sent to user {$reservation->user->id} for reservation {$reservation->id} ({$context})");
                $count++;
            } catch (\Exception $e) {
                Log::error("Failed to send reminder for reservation {$reservation->id}: {$e->getMessage()}");
            }
        }

        Log::info("Processed {$count} reminders for {$context}");

        return $count;
    }
}
