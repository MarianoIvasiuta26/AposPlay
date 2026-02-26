<?php

use Illuminate\Foundation\Inspiring;
use App\Jobs\SendGameReminders;
use App\Console\Commands\SendTestReminder;
use Illuminate\Support\Facades\Schedule;

// Enviar recordatorios cada hora (24h y 1h antes del partido)
Schedule::job(new SendGameReminders)->hourly()
    ->description('Enviar recordatorios de juego');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('reminders:send', function () {
    SendGameReminders::dispatch();
    $this->info('Game reminders dispatched to queue.');
})->purpose('Enviar recordatorios de juego manualmente');
