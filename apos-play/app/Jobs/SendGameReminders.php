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
use Carbon\Carbon;

class SendGameReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Check for 24 hours (approx) and 1 hour (approx)
        // Checks executed hourly

        $start24 = now()->addHours(24)->startOfHour();
        $end24 = now()->addHours(24)->endOfHour();

        $start1 = now()->addHour()->startOfHour();
        $end1 = now()->addHour()->endOfHour();

        $this->processReminders($start24, $end24, '24 horas');
        $this->processReminders($start1, $end1, '1 hora');
    }

    protected function processReminders($start, $end, $context)
    {
        // Need to combine date and time columns for query
        // Or simpler: Iterate over reservations of that DATE and filter by TIME.
        // Given the scale, DB query is better.
        // reservation_date is DATE, start_time is TIME.
        // We look for rows where reservation_date = start->toDateString() AND start_time BETWEEN H:00 and H:59 ?
        // Usually start_time is just H:00:00.

        $targetDate = $start->toDateString();
        $targetHour = $start->format('H'); // 14

        $reservations = Reservation::where('status', ReservationStatus::CONFIRMED->value)
            ->whereDate('reservation_date', $targetDate)
            ->whereRaw("HOUR(start_time) = ?", [$targetHour])
            ->with(['user', 'court'])
            ->get();

        foreach ($reservations as $reservation) {
            $reservation->user->notify(new GameReminder($reservation, $context));
        }
    }
}
