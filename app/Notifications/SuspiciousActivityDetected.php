<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuspiciousActivityDetected extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $activityType;
    protected array $details;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $activityType, array $details = [])
    {
        $this->activityType = $activityType;
        $this->details = $details;
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
        $mail = (new MailMessage)
            ->subject("[SECURITY ALERT] {$this->activityType} - " . config('app.name'))
            ->greeting('⚠️ Security Alert!')
            ->line("Suspicious activity detected: **{$this->activityType}**")
            ->line('**Details:**');

        foreach ($this->details as $key => $value) {
            $mail->line("- **{$key}:** {$value}");
        }

        return $mail
            ->line("- **Time:** " . now()->toDateTimeString())
            ->action('View Security Logs', url('/admin/security-logs'))
            ->line('Please review and take action if necessary.')
            ->salutation('Security System - ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'activity_type' => $this->activityType,
            'details' => $this->details,
        ];
    }
}
