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

        // Check if IP is permanently blocked
        if (Cache::has("blocked_ip:{$ip}")) {
            $this->logSecurityEvent('blocked_ip_access', $request, [
                'reason' => 'IP is blocked'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'IP Anda telah diblokir karena aktivitas mencurigakan. Hubungi administrator.'
            ], 429);
        }

        // Check rate limit
        if (RateLimiter::tooManyAttempts($key, $config['max_attempts'])) {
            $seconds = RateLimiter::availableIn($key);

            $this->logSecurityEvent('brute_force_attempt', $request, [
                'attempts' => RateLimiter::attempts($key),
                'blocked_for' => $seconds
            ]);

            // Block IP after excessive attempts
            $totalAttempts = Cache::increment("login_attempts:{$ip}");

            if ($totalAttempts >= $config['max_attempts'] * 3) {
                Cache::put("blocked_ip:{$ip}", true, now()->addMinutes($config['block_minutes']));

                // Notify admin about brute force attack
                $this->notifyAdmin($request, $totalAttempts);
            }

            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
                'retry_after' => $seconds
            ], 429);
        }

        $response = $next($request);

        // If login failed, increment the counter
        if ($response->getStatusCode() === 401 ||
            (method_exists($response, 'getData') &&
             isset($response->getData()->success) &&
             $response->getData()->success === false)) {
            RateLimiter::hit($key, $config['decay_minutes'] * 60);
        } else {
            // Successful login - clear rate limiter
            RateLimiter::clear($key);
            Cache::forget("login_attempts:{$ip}");
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
