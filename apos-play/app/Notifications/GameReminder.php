<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GameReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public $reservation;
    public $timeContext;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reservation $reservation, string $timeContext)
    {
        $this->reservation = $reservation;
        $this->timeContext = $timeContext;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Recordatorio de partido: ' . $this->reservation->court->name)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Te recordamos que tienes una reserva para jugar en ' . $this->timeContext . '.')
            ->line('Cancha: ' . $this->reservation->court->name)
            ->line('Fecha: ' . $this->reservation->reservation_date->format('d/m/Y'))
            ->line('Hora: ' . $this->reservation->start_time)
            ->line('¡Te esperamos!')
            ->action('Ver mis reservas', route('my-reservations'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
