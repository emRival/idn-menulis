<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SEOMiddleware
{
    /**
     * Handle SEO optimizations
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only process HTML responses
        if (!$this->isHtmlResponse($response)) {
            return $response;
        }

        // Add SEO headers
        $this->addSEOHeaders($response);

        // Handle canonical URL enforcement
        $redirect = $this->handleCanonicalRedirect($request);
        if ($redirect) {
            return $redirect;
        }

        // Minify HTML if enabled
        if (config('seo.performance.minify_html', false) && app()->environment('production')) {
            $this->minifyHtml($response);
        }

        return $response;
    }

    /**
     * Check if response is HTML
     */
    protected function isHtmlResponse(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');
        return str_contains($contentType, 'text/html') || empty($contentType);
    }

    /**
     * Add SEO-related headers
     */
    protected function addSEOHeaders(Response $response): void
    {
        // Enable early hints for critical resources
        if (!$response->headers->has('Link')) {
            $links = [
                sprintf('<%s>; rel=preconnect', 'https://fonts.googleapis.com'),
                sprintf('<%s>; rel=dns-prefetch', 'https://fonts.gstatic.com'),
            ];

            $response->headers->set('Link', implode(', ', $links));
        }

        // Add timing headers for performance monitoring
        $response->headers->set('Server-Timing', 'app;dur=' . round((microtime(true) - LARAVEL_START) * 1000, 2));
    }

    /**
     * Handle canonical URL enforcement (trailing slash, lowercase, etc.)
     */
    protected function handleCanonicalRedirect(Request $request): ?Response
    {
        if (!config('seo.canonical.enforce', true)) {
            return null;
        }

        $path = $request->getPathInfo();
        $originalPath = $path;

        // Skip API and file requests
        if (str_starts_with($path, '/api') || preg_match('/\.[a-z]+$/i', $path)) {
            return null;
        }

        // Remove trailing slash (except for root)
        if (config('seo.canonical.trailing_slash') === false && strlen($path) > 1 && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        // Enforce lowercase URLs
        if (config('seo.canonical.lowercase', true) && preg_match('/[A-Z]/', $path)) {
            $path = strtolower($path);
        }

        // Remove index.php
        if (config('seo.canonical.remove_index', true) && str_contains($path, 'index.php')) {
            $path = str_replace('/index.php', '', $path);
        }

        // Redirect if path changed
        if ($path !== $originalPath) {
            $query = $request->getQueryString();
            $url = $path . ($query ? '?' . $query : '');

            return redirect($url, 301);
        }

        return null;
    }

    /**
     * Minify HTML response
     */
    protected function minifyHtml(Response $response): void
    {
        $content = $response->getContent();

        if (empty($content)) {
            return;
        }

        // Don't minify if it contains pre or code tags
        if (preg_match('/<(pre|code|textarea)/i', $content)) {
            return;
        }

        $search = [
            '/\>[^\S ]+/s',     // Remove whitespace after tags
            '/[^\S ]+\</s',     // Remove whitespace before tags
            '/(\s)+/s',         // Reduce multiple spaces to single space
            '/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', // Remove HTML comments (except IE conditionals)
        ];

        $replace = [
            '>',
            '<',
            '\\1',
            '',
        ];

        $minified = preg_replace($search, $replace, $content);

        if ($minified !== null) {
            $response->setContent($minified);
        }
    }
}
