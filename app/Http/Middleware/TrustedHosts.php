<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class TrustedHosts
{
    /**
     * Handle an incoming request.
     * Validates that the request is coming from a trusted host.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $trustedHosts = config('security.trusted_hosts', []);

        // If no trusted hosts configured, allow all
        if (empty($trustedHosts)) {
            return $next($request);
        }

        $host = $request->getHost();

        // Check if host is trusted
        $isTrusted = false;
        foreach ($trustedHosts as $trustedHost) {
            // Skip empty entries
            if (empty($trustedHost)) continue;
            
            // Exact match
            if ($host === $trustedHost) {
                $isTrusted = true;
                break;
            }
            
            // Subdomain match
            if (str_ends_with($host, '.' . $trustedHost)) {
                $isTrusted = true;
                break;
            }
            
            // Wildcard match (*.domain.com)
            if ($this->matchWildcard($trustedHost, $host)) {
                $isTrusted = true;
                break;
            }
        }

        // Also allow localhost/development environments
        if (!$isTrusted) {
            $developmentHosts = ['127.0.0.1', 'localhost', '::1'];
            $isTrusted = in_array($host, $developmentHosts) ||
                         str_ends_with($host, '.test') ||
                         str_ends_with($host, '.local');
        }

        // In production, just log untrusted hosts but don't block
        // This prevents 403 errors on shared hosting with various domains
        if (!$isTrusted) {
            Log::warning('Request from untrusted host', [
                'host' => $host,
                'ip' => $request->ip(),
                'uri' => $request->getRequestUri(),
            ]);
            
            // OPTIONAL: Uncomment to enforce blocking in production
            // if (app()->environment('production')) {
            //     abort(403, 'Untrusted host');
            // }
        }

        return $next($request);
    }

    /**
     * Match wildcard pattern
     */
    protected function matchWildcard(string $pattern, string $host): bool
    {
        if (str_starts_with($pattern, '*.')) {
            $domain = substr($pattern, 2);
            return $host === $domain || str_ends_with($host, '.' . $domain);
        }

        return false;
    }
}
