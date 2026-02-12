<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionSecurity
{
    /**
     * Handle session security - prevent hijacking.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $sessionKey = 'session_security';

            // Get stored session data
            $storedData = session($sessionKey);
            $currentData = $this->getSessionData($request);

            if ($storedData) {
                // Verify session integrity
                if (!$this->verifySession($storedData, $currentData, $request)) {
                    $this->handleSessionAnomaly($request, $user, $storedData, $currentData);

                    // Force logout
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->with('error', 'Sesi Anda telah berakhir karena alasan keamanan. Silakan login kembali.');
                }
            }

            // Store/update session data
            session([$sessionKey => $currentData]);

            // Regenerate session ID periodically
            if ($this->shouldRegenerateSession()) {
                $request->session()->regenerate();
                session(['last_regeneration' => now()->timestamp]);
            }

            // Check session timeout
            if ($this->isSessionTimedOut()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Sesi Anda telah berakhir karena tidak aktif. Silakan login kembali.');
            }

            // Update last activity
            session(['last_activity' => now()->timestamp]);
        }

        return $next($request);
    }

    /**
     * Get current session data for verification.
     */
    protected function getSessionData(Request $request): array
    {
        return [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_agent_hash' => md5($request->userAgent()),
        ];
    }

    /**
     * Verify session integrity.
     */
    protected function verifySession(array $stored, array $current, Request $request): bool
    {
        // Allow IP changes (mobile networks can change IPs)
        // But monitor significant changes

        // User agent should remain the same
        if ($stored['user_agent_hash'] !== $current['user_agent_hash']) {
            return false;
        }

        // Check for suspicious IP change patterns
        if ($stored['ip'] !== $current['ip']) {
            // Log the IP change but don't immediately invalidate
            // This handles legitimate cases like mobile networks
            Log::channel('security')->info('Session IP changed', [
                'user_id' => Auth::id(),
                'old_ip' => $stored['ip'],
                'new_ip' => $current['ip'],
            ]);

            // If IP changed to a completely different region, it might be suspicious
            // For now, we allow it but log it
        }

        return true;
    }

    /**
     * Handle session anomaly.
     */
    protected function handleSessionAnomaly(Request $request, $user, array $stored, array $current): void
    {
        Log::channel('security')->warning('Session hijacking attempt detected', [
            'user_id' => $user->id,
            'email' => $user->email,
            'stored_ip' => $stored['ip'],
            'current_ip' => $current['ip'],
            'stored_user_agent' => $stored['user_agent'] ?? 'unknown',
            'current_user_agent' => $current['user_agent'],
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Check if session should be regenerated.
     */
    protected function shouldRegenerateSession(): bool
    {
        $lastRegeneration = session('last_regeneration', 0);
        $regenerateInterval = 30 * 60; // 30 minutes

        return (now()->timestamp - $lastRegeneration) > $regenerateInterval;
    }

    /**
     * Check if session has timed out.
     */
    protected function isSessionTimedOut(): bool
    {
        $lastActivity = session('last_activity');

        if (!$lastActivity) {
            return false;
        }

        $timeout = config('security.session.timeout_minutes', 120) * 60;

        return (now()->timestamp - $lastActivity) > $timeout;
    }
}
