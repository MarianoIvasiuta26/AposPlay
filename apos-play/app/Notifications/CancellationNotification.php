<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CancellationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Reservation $reservation
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $court = $this->reservation->court;
        $user = $this->reservation->user;
        $date = $this->reservation->reservation_date->format('d/m/Y');
        $time = substr($this->reservation->start_time, 0, 5);

        return (new MailMessage)
            ->subject("Reserva cancelada - {$court->name}")
            ->greeting("Hola {$notifiable->name},")
            ->line("Se ha cancelado una reserva en tu complejo.")
            ->line("**Cancha:** {$court->name}")
            ->line("**Fecha:** {$date}")
            ->line("**Hora:** {$time} hs")
            ->line("**Usuario:** {$user->name} ({$user->email})")
            ->action('Ver reservas', route('staff.reservations'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'court' => $this->reservation->court->name,
            'user' => $this->reservation->user->name,
            'message' => "Reserva cancelada en {$this->reservation->court->name}",
        ];
    }
}
