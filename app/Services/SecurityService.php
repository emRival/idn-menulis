<?php

namespace App\Services;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SecurityService
{
    /**
     * Encrypt sensitive data.
     */
    public function encrypt(string $value): string
    {
        return Crypt::encryptString($value);
    }

    /**
     * Decrypt sensitive data.
     */
    public function decrypt(string $encryptedValue): string
    {
        try {
            return Crypt::decryptString($encryptedValue);
        } catch (\Exception $e) {
            Log::error('Decryption failed: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Hash password with Argon2.
     */
    public function hashPassword(string $password): string
    {
        return Hash::make($password, [
            'memory' => 65536,
            'time' => 4,
            'threads' => 2,
        ]);
    }

    /**
     * Verify password.
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }

    /**
     * Generate secure random token.
     */
    public function generateSecureToken(int $length = 64): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Generate password reset token with expiration.
     */
    public function generatePasswordResetToken(User $user): string
    {
        $token = $this->generateSecureToken();

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        return $token;
    }

    /**
     * Verify password reset token.
     */
    public function verifyPasswordResetToken(string $email, string $token): bool
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record) {
            return false;
        }

        // Check expiration (60 minutes)
        $createdAt = \Carbon\Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return false;
        }

        return Hash::check($token, $record->token);
    }

    /**
     * Log user activity.
     */
    public function logActivity(
        ?User $user,
        string $action,
        ?string $model = null,
        ?int $modelId = null,
        array $details = [],
        ?Request $request = null
    ): void {
        if (!config('security.features.activity_logging')) {
            return;
        }

        $data = [
            'user_id' => $user?->id,
            'action' => $action,
            'model_type' => $model,
            'model_id' => $modelId,
            'details' => json_encode($details),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'created_at' => now(),
        ];

        try {
            ActivityLog::create($data);
        } catch (\Exception $e) {
            Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }

    /**
     * Log security event.
     */
    public function logSecurityEvent(
        string $event,
        array $context = [],
        string $level = 'warning'
    ): void {
        $context['timestamp'] = now()->toDateTimeString();

        Log::channel('security')->{$level}("Security: {$event}", $context);
    }

    /**
     * Block IP address.
     */
    public function blockIP(string $ip, int $minutes = 60, string $reason = ''): void
    {
        Cache::put("blocked_ip:{$ip}", [
            'blocked_at' => now(),
            'reason' => $reason,
            'expires_at' => now()->addMinutes($minutes),
        ], now()->addMinutes($minutes));

        $this->logSecurityEvent('ip_blocked', [
            'ip' => $ip,
            'minutes' => $minutes,
            'reason' => $reason,
        ]);
    }

    /**
     * Unblock IP address.
     */
    public function unblockIP(string $ip): void
    {
        Cache::forget("blocked_ip:{$ip}");

        $this->logSecurityEvent('ip_unblocked', [
            'ip' => $ip,
        ], 'info');
    }

    /**
     * Check if IP is blocked.
     */
    public function isIPBlocked(string $ip): bool
    {
        return Cache::has("blocked_ip:{$ip}");
    }

    /**
     * Invalidate all sessions for user (logout everywhere).
     */
    public function invalidateAllSessions(User $user): void
    {
        // Update remember token to invalidate "remember me" sessions
        $user->update([
            'remember_token' => null,
        ]);

        // If using database sessions, delete user's sessions
        if (config('session.driver') === 'database') {
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();
        }

        $this->logActivity($user, 'logout_all_devices');
    }

    /**
     * Validate password strength.
     */
    public function validatePasswordStrength(string $password): array
    {
        $config = config('security.password');
        $errors = [];

        if (strlen($password) < $config['min_length']) {
            $errors[] = "Password harus minimal {$config['min_length']} karakter.";
        }

        if ($config['require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password harus mengandung huruf kapital.';
        }

        if ($config['require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password harus mengandung huruf kecil.';
        }

        if ($config['require_number'] && !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password harus mengandung angka.';
        }

        if ($config['require_special'] && !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = 'Password harus mengandung karakter khusus.';
        }

        // Check against common passwords
        if ($this->isCommonPassword($password)) {
            $errors[] = 'Password terlalu umum, gunakan password yang lebih unik.';
        }

        return $errors;
    }

    /**
     * Check if password is common.
     */
    protected function isCommonPassword(string $password): bool
    {
        $commonPasswords = [
            'password', '123456', '12345678', 'qwerty', 'abc123',
            'password123', 'admin', 'letmein', 'welcome', 'monkey',
            '1234567890', 'password1', 'sunshine', 'princess', 'admin123',
        ];

        return in_array(strtolower($password), $commonPasswords);
    }

    /**
     * Generate Two-Factor Authentication secret.
     */
    public function generate2FASecret(): string
    {
        return $this->generateSecureToken(32);
    }

    /**
     * Generate recovery codes for 2FA.
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];

        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(4) . '-' . Str::random(4));
        }

        return $codes;
    }

    /**
     * Sanitize filename for safe storage.
     */
    public function sanitizeFilename(string $filename): string
    {
        // Remove path traversal
        $filename = basename($filename);

        // Remove dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);

        // Prevent double extensions
        $parts = explode('.', $filename);
        if (count($parts) > 2) {
            $extension = array_pop($parts);
            $name = implode('_', $parts);
            $filename = $name . '.' . $extension;
        }

        // Limit length
        if (strlen($filename) > 255) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $name = substr(pathinfo($filename, PATHINFO_FILENAME), 0, 250 - strlen($extension));
            $filename = $name . '.' . $extension;
        }

        return $filename;
    }

    /**
     * Generate unique filename.
     */
    public function generateUniqueFilename(string $originalFilename): string
    {
        $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $uniqueName = Str::uuid() . '_' . time();

        return $uniqueName . '.' . $extension;
    }

    /**
     * Check for suspicious activity patterns.
     */
    public function detectSuspiciousActivity(Request $request, User $user = null): bool
    {
        $ip = $request->ip();
        $key = "request_count:{$ip}";

        // Count requests in last minute
        $count = Cache::increment($key);

        if ($count === 1) {
            Cache::put($key, 1, 60);
        }

        // More than 100 requests per minute is suspicious
        if ($count > 100) {
            $this->logSecurityEvent('high_request_rate', [
                'ip' => $ip,
                'count' => $count,
                'user_id' => $user?->id,
            ]);
            return true;
        }

        return false;
    }
}
