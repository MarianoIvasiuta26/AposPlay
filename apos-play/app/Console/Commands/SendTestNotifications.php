<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use App\Models\Reservation;
use App\Models\User;
use App\Notifications\CancellationNotification;
use App\Notifications\CouponAssigned;
use App\Notifications\GameReminder;
use Illuminate\Console\Command;

class SendTestNotifications extends Command
{
    protected $signature = 'notifications:test
                           {email? : Email del usuario (default: cliente@aposplay.dev)}
                           {--type= : Tipo: reminder|cancellation|coupon (default: envía las tres)}';

    protected $description = 'Envía todas las notificaciones de prueba directamente a un usuario (sin queue)';

    public function handle(): int
    {
        $email = $this->argument('email') ?? 'cliente@aposplay.dev';

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No se encontró el usuario: {$email}");
            return Command::FAILURE;
        }

        $type = $this->option('type');

        $this->info("Enviando notificaciones de prueba a: {$user->name} <{$user->email}>");
        if ($type) {
            $this->line("  Tipo: {$type}");
        }
        $this->line('');

        // ── 1. GameReminder ───────────────────────────────────────────────
        if (!$type || $type === 'reminder') {
            $reservation = Reservation::where('user_id', $user->id)
                ->where('status', 'confirmed')
                ->whereDate('reservation_date', '>=', now()->toDateString())
                ->with(['court'])
                ->first()
                ?? Reservation::where('status', 'confirmed')->with(['court'])->first();

            if ($reservation) {
                $this->line('  → Enviando GameReminder (recordatorio de partido)...');
                try {
                    $user->notifyNow(new GameReminder($reservation, '24 horas'));
                    $this->info('  ✓ GameReminder enviado');
                } catch (\Exception $e) {
                    $this->error('  ✗ Error: ' . $e->getMessage());
                    $this->warn('  → Reintentá con: php artisan notifications:test --type=reminder');
                }
            } else {
                $this->warn('  ✗ No hay reservas confirmadas para GameReminder');
            }
        }

        // ── 2. CancellationNotification ───────────────────────────────────
        if (!$type || $type === 'cancellation') {
            $cancelled = Reservation::where('status', 'cancelled')
                ->with(['court', 'user'])
                ->first();

            if ($cancelled) {
                $this->line('  → Enviando CancellationNotification...');
                try {
                    $user->notifyNow(new CancellationNotification($cancelled));
                    $this->info('  ✓ CancellationNotification enviada');
                } catch (\Exception $e) {
                    $this->error('  ✗ Error: ' . $e->getMessage());
                    $this->warn('  → Reintentá con: php artisan notifications:test --type=cancellation');
                }
            } else {
                $this->warn('  ✗ No hay reservas canceladas para CancellationNotification');
            }
        }

        // ── 3. CouponAssigned ─────────────────────────────────────────────
        if (!$type || $type === 'coupon') {
            $coupon = Coupon::first();

            if ($coupon) {
                $this->line('  → Enviando CouponAssigned...');
                try {
                    $user->notifyNow(new CouponAssigned($coupon));
                    $this->info('  ✓ CouponAssigned enviada');
                } catch (\Exception $e) {
                    $this->error('  ✗ Error: ' . $e->getMessage());
                    $this->warn('  → Reintentá con: php artisan notifications:test --type=coupon');
                }
            } else {
                $this->warn('  ✗ No hay cupones disponibles para CouponAssigned');
            }
        }

        $this->line('');
        $this->info('Listo. Revisá tu bandeja en Mailtrap.');

        return Command::SUCCESS;
    }
}
