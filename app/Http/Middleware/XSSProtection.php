<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class XSSProtection
{
    /**
     * Fields yang diizinkan mengandung HTML (contoh: editor WYSIWYG).
     */
    protected array $htmlAllowedFields = [
        'content',
        'body',
        'description',
        'bio',
    ];

    /**
     * Fields yang tidak perlu di-sanitize.
     */
    protected array $skipFields = [
        'password',
        'password_confirmation',
        '_token',
        '_method',
    ];

    /**
     * Handle XSS protection.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        $sanitized = $this->sanitizeInput($input);
        $request->merge($sanitized);

        return $next($request);
    }

    /**
     * Recursively sanitize input.
     */
    protected function sanitizeInput(array $input, string $prefix = ''): array
    {
        foreach ($input as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (in_array($key, $this->skipFields)) {
                continue;
            }

            if (is_array($value)) {
                $input[$key] = $this->sanitizeInput($value, $fullKey);
            } elseif (is_string($value)) {
                if (in_array($key, $this->htmlAllowedFields)) {
                    $input[$key] = $this->sanitizeHtml($value);
                } else {
                    $input[$key] = $this->sanitizePlainText($value);
                }
            }
        }

        return $input;
    }

    /**
     * Sanitize plain text input.
     */
    protected function sanitizePlainText(string $value): string
    {
        // Remove null bytes
        $value = str_replace(chr(0), '', $value);

        // Remove all HTML tags
        $value = strip_tags($value);

        // Remove javascript: protocol
        $value = preg_replace('/javascript\s*:/i', '', $value);

        // Remove data: protocol (can be used for XSS)
        $value = preg_replace('/data\s*:/i', '', $value);

        // Remove vbscript: protocol
        $value = preg_replace('/vbscript\s*:/i', '', $value);

        // Encode special characters
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);

        return $value;
    }

    /**
     * Sanitize HTML content (for WYSIWYG editors).
     */
    protected function sanitizeHtml(string $value): string
    {
        // Remove null bytes
        $value = str_replace(chr(0), '', $value);

        // Remove script tags
        $value = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $value);

        // Remove javascript: protocol
        $value = preg_replace('/javascript\s*:/i', '', $value);

        // Remove on* event attributes (onclick, onerror, etc.)
        $value = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $value);
        $value = preg_replace('/\s*on\w+\s*=\s*[^\s>]*/i', '', $value);

        // Remove data: protocol in src/href attributes
        $value = preg_replace('/(<[^>]+)(src|href)\s*=\s*["\']?\s*data:/i', '$1$2="', $value);

        // Remove vbscript: protocol
        $value = preg_replace('/vbscript\s*:/i', '', $value);

        // Remove style tags (can contain expressions)
        $value = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $value);

        // Remove link tags (can load external stylesheets)
        $value = preg_replace('/<link\b[^>]*>/i', '', $value);

        // Remove meta tags
        $value = preg_replace('/<meta\b[^>]*>/i', '', $value);

        // Remove base tags
        $value = preg_replace('/<base\b[^>]*>/i', '', $value);

        // Remove object/embed/applet tags
        $value = preg_replace('/<(object|embed|applet)\b[^>]*>(.*?)<\/\1>/is', '', $value);
        $value = preg_replace('/<(object|embed|applet)\b[^>]*\/?>/i', '', $value);

        // Remove iframe (unless you specifically need them)
        $value = preg_replace('/<iframe\b[^>]*>(.*?)<\/iframe>/is', '', $value);

        // Remove form tags
        $value = preg_replace('/<\/?form\b[^>]*>/i', '', $value);

        // Remove input/button/select/textarea (form elements)
        $value = preg_replace('/<(input|button|select|textarea)\b[^>]*\/?>/i', '', $value);

        return $value;
    }

    /**
     * Detect potential XSS patterns.
     */
    protected function detectXSSPatterns(string $value): bool
    {
        $patterns = [
            '/<script/i',
            '/javascript\s*:/i',
            '/on\w+\s*=/i',
            '/expression\s*\(/i',
            '/vbscript\s*:/i',
            '/data\s*:.*base64/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }
}
