<?php

use Illuminate\Foundation\Inspiring;
use App\Jobs\SendGameReminders;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new SendGameReminders)->hourly();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
