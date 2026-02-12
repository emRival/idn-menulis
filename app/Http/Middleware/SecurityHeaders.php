<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Security headers for protection against various attacks.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only apply headers to responses that support headers
        if (method_exists($response, 'headers')) {
            // Prevent Clickjacking
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

            // XSS Protection (legacy but still useful)
            $response->headers->set('X-XSS-Protection', '1; mode=block');

            // Prevent MIME type sniffing
            $response->headers->set('X-Content-Type-Options', 'nosniff');

            // Referrer Policy
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

            // HSTS - Force HTTPS
            if ($request->secure() || config('app.env') === 'production') {
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            }

            // Content Security Policy
            $response->headers->set('Content-Security-Policy', $this->getCSP());

            // Permissions Policy (Feature Policy replacement)
            $response->headers->set('Permissions-Policy', $this->getPermissionsPolicy());

            // Cross-Origin policies
            $response->headers->set('Cross-Origin-Embedder-Policy', 'unsafe-none');
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');
            $response->headers->set('Cross-Origin-Resource-Policy', 'cross-origin');

            // Cache control for sensitive pages
            if ($this->isSensitivePage($request)) {
                $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
                $response->headers->set('Pragma', 'no-cache');
                $response->headers->set('Expires', '0');
            }

            // Remove server signature
            $response->headers->remove('X-Powered-By');
            $response->headers->remove('Server');
        }

        return $response;
    }

    /**
     * Generate Content Security Policy.
     */
    private function getCSP(): string
    {
        // Trusted origins from config
        $trustedOrigins = implode(' ', config('security.trusted_origins', []));

        $connectSrc = "'self' https://api.idnmenulis.com {$trustedOrigins}";

        // Allow localhost in development
        if (config('app.env') !== 'production') {
            $connectSrc .= " http://localhost:* http://127.0.0.1:* ws://localhost:* ws://127.0.0.1:*";
        }

        return implode('; ', [
            "default-src 'self' {$trustedOrigins}",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com https://cdn.tiny.cloud",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.tiny.cloud",
            "img-src 'self' data: https: blob: {$trustedOrigins}",
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:",
            "connect-src {$connectSrc}",
            "media-src 'self' blob:",
            "object-src 'none'",
            "frame-src 'self' https://www.youtube.com https://player.vimeo.com {$trustedOrigins}",
            "frame-ancestors 'self' {$trustedOrigins}",
            "form-action 'self' {$trustedOrigins}",
            "base-uri 'self'",
            "upgrade-insecure-requests",
            "block-all-mixed-content"
        ]);
    }

    /**
     * Generate Permissions Policy.
     */
    private function getPermissionsPolicy(): string
    {
        return implode(', ', [
            'geolocation=()',
            'microphone=()',
            'camera=()',
            'payment=()',
            'usb=()',
            'magnetometer=()',
            'gyroscope=()',
            'accelerometer=()'
        ]);
    }

    /**
     * Check if current page is sensitive.
     */
    private function isSensitivePage(Request $request): bool
    {
        $sensitiveRoutes = [
            'login',
            'register',
            'password',
            'admin',
            'profile',
            'settings',
            'account',
        ];

        foreach ($sensitiveRoutes as $route) {
            if (str_contains($request->path(), $route)) {
                return true;
            }
        }

        return false;
    }
}
