<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Force HTTPS in production.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for local development
        if (app()->environment('local')) {
            return $next($request);
        }

        // Skip if already HTTPS
        if ($request->secure()) {
            return $next($request);
        }

        // Skip for health checks
        if ($request->is('up', 'health', 'api/health')) {
            return $next($request);
        }

        // Redirect to HTTPS
        return redirect()->secure($request->getRequestUri(), 301);
    }
}
