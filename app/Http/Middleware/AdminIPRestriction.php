<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminIPRestriction
{
    /**
     * Handle admin IP restriction.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $whitelist = config('security.admin_ip_whitelist', []);

        // If whitelist is empty, allow all
        if (empty($whitelist)) {
            return $next($request);
        }

        $clientIP = $request->ip();

        // Check if IP is in whitelist
        if (!$this->isIPAllowed($clientIP, $whitelist)) {
            Log::channel('security')->warning('Admin access denied - IP not whitelisted', [
                'ip' => $clientIP,
                'url' => $request->fullUrl(),
                'user_id' => auth()->id(),
                'timestamp' => now()->toDateTimeString(),
            ]);

            abort(403, 'Akses ditolak. IP Anda tidak diizinkan mengakses halaman ini.');
        }

        return $next($request);
    }

    /**
     * Check if IP is allowed (supports CIDR notation).
     */
    protected function isIPAllowed(string $ip, array $whitelist): bool
    {
        foreach ($whitelist as $allowed) {
            if ($this->ipMatch($ip, $allowed)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Match IP against pattern (supports CIDR).
     */
    protected function ipMatch(string $ip, string $pattern): bool
    {
        // Exact match
        if ($ip === $pattern) {
            return true;
        }

        // CIDR notation
        if (strpos($pattern, '/') !== false) {
            list($subnet, $mask) = explode('/', $pattern);

            $ip_long = ip2long($ip);
            $subnet_long = ip2long($subnet);
            $mask_long = ~((1 << (32 - $mask)) - 1);

            return ($ip_long & $mask_long) === ($subnet_long & $mask_long);
        }

        // Wildcard notation (e.g., 192.168.1.*)
        if (strpos($pattern, '*') !== false) {
            $pattern = str_replace('.', '\.', $pattern);
            $pattern = str_replace('*', '\d+', $pattern);
            return preg_match('/^' . $pattern . '$/', $ip);
        }

        return false;
    }
}
