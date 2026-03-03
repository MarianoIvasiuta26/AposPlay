<?php

namespace App\Notifications;

use App\Models\Coupon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CouponAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public Coupon $coupon;

    /**
     * Create a new notification instance.
     */
    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $discountText = $this->coupon->formattedValue();

        return (new MailMessage)
            ->subject('¡Tenés un nuevo cupón de descuento!')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Te asignamos un cupón de descuento para tu próxima reserva.')
            ->line('')
            ->line('**Detalles del cupón:**')
            ->line('• Código: **' . $this->coupon->code . '**')
            ->line('• Descuento: ' . $discountText)
            ->line('• Descripción: ' . $this->coupon->description)
            ->line('')
            ->line($this->coupon->valid_until
                ? '⏰ Válido hasta: ' . $this->coupon->valid_until->format('d/m/Y')
                : '✅ Sin fecha de expiración')
            ->line('')
            ->line('¡Usalo en tu próxima reserva!')
            ->action('Reservar ahora', route('court-availability'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'coupon_id' => $this->coupon->id,
            'coupon_code' => $this->coupon->code,
            'message' => '¡Tenés un nuevo cupón de descuento! Código: ' . $this->coupon->code . ' (' . $this->coupon->formattedValue() . ')',
        ];
    }
}
