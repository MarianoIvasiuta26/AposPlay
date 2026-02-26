<?php

namespace App\Console\Commands;

use App\Jobs\SendGameReminders;
use Illuminate\Console\Command;

class SendTestReminder extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     */
    protected $description = 'Enviar recordatorios de juego manualmente (24h y 1h antes)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Sending game reminders...');

        SendGameReminders::dispatch();

        $this->info('Game reminders dispatched to queue.');

        return Command::SUCCESS;
    }
}