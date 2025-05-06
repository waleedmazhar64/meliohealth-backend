<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForgetPassword extends Notification
{
    use Queueable;

    public $token;
    public $name;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($name, $token)
    {
        $this->name = $name;
        $this->token = $token;
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
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('MelioHealth Reset Password')
            ->greeting('Hi '. $this->name.',')
            ->line('Forgot your password? No problem. Just click on this secure link:')
            ->action('Reset Password',$this->token)
            ->line('If you did not request a password reset, you may safely ignore this message. Your account remains safe and your current password will not be changed.')
            ->line('Best regards,')
            ->line('Your team at MelioHealth.') 
            ->line('https://meliohealth.ai.');
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
