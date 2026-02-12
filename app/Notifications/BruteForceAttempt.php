<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BruteForceAttempt extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $ip;
    protected ?string $email;
    protected int $attempts;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $ip, ?string $email = null, int $attempts = 0)
    {
        $this->ip = $ip;
        $this->email = $email;
        $this->attempts = $attempts;
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
            ->subject('[SECURITY ALERT] Brute Force Attack Detected - ' . config('app.name'))
            ->greeting('⚠️ Security Alert!')
            ->line('A brute force attack has been detected on your application.')
            ->line('**Details:**')
            ->line("- **IP Address:** {$this->ip}")
            ->line("- **Target Email:** " . ($this->email ?? 'Multiple'))
            ->line("- **Failed Attempts:** {$this->attempts}")
            ->line("- **Time:** " . now()->toDateTimeString())
            ->line('The IP address has been automatically blocked for 1 hour.')
            ->action('View Security Logs', url('/admin/security-logs'))
            ->line('Please review the security logs and take appropriate action if needed.')
            ->salutation('Security System - ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ip' => $this->ip,
            'email' => $this->email,
            'attempts' => $this->attempts,
            'type' => 'brute_force_attempt',
        ];
    }
}
