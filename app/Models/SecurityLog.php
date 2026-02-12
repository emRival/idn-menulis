<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_type',
        'ip_address',
        'user_agent',
        'location',
        'details',
        'severity',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    /**
     * Event types constants.
     */
    const EVENT_LOGIN = 'login';
    const EVENT_LOGOUT = 'logout';
    const EVENT_FAILED_LOGIN = 'failed_login';
    const EVENT_PASSWORD_CHANGE = 'password_change';
    const EVENT_PASSWORD_RESET = 'password_reset';
    const EVENT_2FA_ENABLED = '2fa_enabled';
    const EVENT_2FA_DISABLED = '2fa_disabled';
    const EVENT_2FA_FAILED = '2fa_failed';
    const EVENT_ACCOUNT_LOCKED = 'account_locked';
    const EVENT_SUSPICIOUS_ACTIVITY = 'suspicious_activity';
    const EVENT_SESSION_HIJACK_ATTEMPT = 'session_hijack_attempt';
    const EVENT_SQL_INJECTION_ATTEMPT = 'sql_injection_attempt';
    const EVENT_XSS_ATTEMPT = 'xss_attempt';
    const EVENT_BRUTE_FORCE_ATTEMPT = 'brute_force_attempt';
    const EVENT_FILE_UPLOAD_VIOLATION = 'file_upload_violation';

    /**
     * Get the user that owns the security log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for filtering by severity.
     */
    public function scopeSeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope for filtering by event type.
     */
    public function scopeEventType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope for filtering by IP address.
     */
    public function scopeFromIP($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Scope for recent logs.
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Log a security event.
     */
    public static function log(
        string $eventType,
        ?int $userId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        array $details = [],
        string $severity = 'info'
    ): self {
        return self::create([
            'user_id' => $userId,
            'event_type' => $eventType,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'details' => $details,
            'severity' => $severity,
        ]);
    }

    /**
     * Get critical events in last 24 hours.
     */
    public static function getCriticalEventsCount(): int
    {
        return self::where('severity', 'critical')
            ->where('created_at', '>=', now()->subHours(24))
            ->count();
    }

    /**
     * Get suspicious IPs.
     */
    public static function getSuspiciousIPs(int $threshold = 10): array
    {
        return self::where('severity', '!=', 'info')
            ->where('created_at', '>=', now()->subHours(24))
            ->groupBy('ip_address')
            ->havingRaw('COUNT(*) >= ?', [$threshold])
            ->pluck('ip_address')
            ->toArray();
    }
}
