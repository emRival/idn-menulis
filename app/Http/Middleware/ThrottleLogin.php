<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class ThrottleLogin
{
    /**
     * Handle brute force protection for login attempts.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $config = config('security.rate_limits.login');
        $key = $this->throttleKey($request);
        $ip = $request->ip();
        $email = strtolower($request->input('email', ''));

        // Check if this specific email+IP combination is blocked
        if ($email && Cache::has("blocked_login:{$ip}:{$email}")) {
            $this->logSecurityEvent('blocked_login_access', $request, [
                'reason' => 'Email+IP combination is blocked'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Akun Anda telah diblokir sementara karena terlalu banyak percobaan login gagal. Silakan coba lagi nanti atau hubungi administrator.'
            ], 429);
        }

        // Check if entire IP is blocked (extreme brute-force across multiple emails)
        if (Cache::has("blocked_ip:{$ip}")) {
            $this->logSecurityEvent('blocked_ip_access', $request, [
                'reason' => 'IP is blocked due to extreme brute-force'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'IP Anda telah diblokir karena aktivitas mencurigakan. Hubungi administrator.'
            ], 429);
        }

        // Check rate limit per email+IP
        if (RateLimiter::tooManyAttempts($key, $config['max_attempts'])) {
            $seconds = RateLimiter::availableIn($key);

            $this->logSecurityEvent('brute_force_attempt', $request, [
                'attempts' => RateLimiter::attempts($key),
                'blocked_for' => $seconds
            ]);

            // Block this specific email+IP after excessive attempts
            $emailIpAttempts = Cache::increment("login_attempts:{$ip}:{$email}");

            if ($emailIpAttempts >= $config['max_attempts'] * 3) {
                Cache::put("blocked_login:{$ip}:{$email}", true, now()->addMinutes($config['block_minutes']));

                // Also track total attempts from this IP across all emails
                $totalIpAttempts = Cache::increment("login_attempts_total:{$ip}");

                // Only block entire IP if extreme brute-force across multiple emails
                if ($totalIpAttempts >= $config['max_attempts'] * 10) {
                    Cache::put("blocked_ip:{$ip}", true, now()->addMinutes($config['block_minutes']));
                    $this->notifyAdmin($request, $totalIpAttempts);
                }
            }

            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
                'retry_after' => $seconds
            ], 429);
        }

        $response = $next($request);

        // If login failed, increment the counter
        if (
            $response->getStatusCode() === 401 ||
            (method_exists($response, 'getData') &&
                isset($response->getData()->success) &&
                $response->getData()->success === false)
        ) {
            RateLimiter::hit($key, $config['decay_minutes'] * 60);
        } else {
            // Successful login - clear rate limiter for this email+IP
            RateLimiter::clear($key);
            Cache::forget("login_attempts:{$ip}:{$email}");
            Cache::forget("blocked_login:{$ip}:{$email}");
        }

        return $response;
    }

    /**
     * Generate throttle key.
     */
    protected function throttleKey(Request $request): string
    {
        $email = strtolower($request->input('email', ''));
        return 'login_throttle:' . sha1($request->ip() . '|' . $email);
    }

    /**
     * Log security event.
     */
    protected function logSecurityEvent(string $event, Request $request, array $context = []): void
    {
        if (config('security.features.activity_logging')) {
            Log::channel('security')->warning("Security Event: {$event}", array_merge([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'email' => $request->input('email'),
                'timestamp' => now()->toDateTimeString()
            ], $context));
        }
    }

    /**
     * Notify admin about brute force attack.
     */
    protected function notifyAdmin(Request $request, int $attempts): void
    {
        $adminEmail = config('security.admin_email');

        if ($adminEmail && config('security.features.suspicious_detection')) {
            try {
                Mail::raw(
                    "Brute force attack detected!\n\n" .
                    "IP: {$request->ip()}\n" .
                    "User Agent: {$request->userAgent()}\n" .
                    "Email Targeted: {$request->input('email')}\n" .
                    "Total Attempts: {$attempts}\n" .
                    "Time: " . now()->toDateTimeString(),
                    function ($message) use ($adminEmail) {
                        $message->to($adminEmail)
                            ->subject('[SECURITY ALERT] Brute Force Attack Detected');
                    }
                );
            } catch (\Exception $e) {
                Log::error('Failed to send brute force notification: ' . $e->getMessage());
            }
        }
    }
}
