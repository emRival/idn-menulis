<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'successful',
        'failure_reason',
        'attempted_at',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    /**
     * Record a login attempt.
     */
    public static function record(
        ?string $email,
        string $ipAddress,
        ?string $userAgent,
        bool $successful,
        ?string $failureReason = null
    ): self {
        return self::create([
            'email' => $email,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'successful' => $successful,
            'failure_reason' => $failureReason,
            'attempted_at' => now(),
        ]);
    }

    /**
     * Get failed attempts count for email/IP.
     */
    public static function getFailedAttemptsCount(
        string $ipAddress,
        ?string $email = null,
        int $minutes = 60
    ): int {
        $query = self::where('ip_address', $ipAddress)
            ->where('successful', false)
            ->where('attempted_at', '>=', now()->subMinutes($minutes));

        if ($email) {
            $query->where('email', $email);
        }

        return $query->count();
    }

    /**
     * Check if IP should be blocked.
     */
    public static function shouldBlockIP(string $ipAddress, int $threshold = 10): bool
    {
        return self::getFailedAttemptsCount($ipAddress, null, 60) >= $threshold;
    }

    /**
     * Get most targeted emails.
     */
    public static function getMostTargetedEmails(int $hours = 24, int $limit = 10): array
    {
        return self::where('successful', false)
            ->where('attempted_at', '>=', now()->subHours($hours))
            ->whereNotNull('email')
            ->groupBy('email')
            ->orderByRaw('COUNT(*) DESC')
            ->limit($limit)
            ->pluck('email')
            ->toArray();
    }

    /**
     * Get most suspicious IPs.
     */
    public static function getMostSuspiciousIPs(int $hours = 24, int $limit = 10): array
    {
        return self::where('successful', false)
            ->where('attempted_at', '>=', now()->subHours($hours))
            ->groupBy('ip_address')
            ->orderByRaw('COUNT(*) DESC')
            ->limit($limit)
            ->selectRaw('ip_address, COUNT(*) as attempts')
            ->get()
            ->toArray();
    }

    /**
     * Clean old records.
     */
    public static function cleanOldRecords(int $days = 30): int
    {
        return self::where('attempted_at', '<', now()->subDays($days))->delete();
    }
}
