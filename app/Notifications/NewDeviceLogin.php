<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDeviceLogin extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $ip;
    protected string $userAgent;
    protected string $location;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $ip, string $userAgent, string $location = 'Unknown')
    {
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->location = $location;
    }

    /**
     * Get the notification's delivery channels.
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
            ->subject('New Login to Your Account - ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We detected a new login to your account.')
            ->line('**Login Details:**')
            ->line("- **Time:** " . now()->toDateTimeString())
            ->line("- **IP Address:** {$this->ip}")
            ->line("- **Device:** {$this->userAgent}")
            ->line("- **Location:** {$this->location}")
            ->line('If this was you, no action is needed.')
            ->line('If you did not login, please secure your account immediately:')
            ->action('Change Password', url('/profile/security'))
            ->line('You can also enable Two-Factor Authentication for extra security.')
            ->salutation('Security Team - ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ip' => $this->ip,
            'user_agent' => $this->userAgent,
            'location' => $this->location,
            'type' => 'new_device_login',
        ];
    }
}
