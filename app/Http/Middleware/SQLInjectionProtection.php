<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use App\Jobs\SendSecurityWebhook;
use Symfony\Component\HttpFoundation\Response;

class SQLInjectionProtection
{
    /**
     * SQL Injection patterns to detect.
     */
    protected array $patterns = [
        // SQL injection with encoding - specific patterns
        // '/(\%27)|(\')|(\-\-)|(\%23)|(#)/i',  // Too broad, disabled

        // SQL comment attacks
        '/\-\-\s*$/i',           // -- at end (SQL comment)
        '/\#\s*$/i',             // # at end (MySQL comment)

        // SQL injection with encoding
        '/((\%3D)|(=))[^\n]*((\%27)|(\-\-)|(\%3B))/i',

        // SQL injection using OR/AND with quotes
        '/\'\s*(or|and)\s*\'?\d*\s*=\s*\'?\d*/i',
        '/\'\s*(or|and)\s*\'[^\']+\'\s*=\s*\'[^\']+\'/i',

        // UNION attacks
        '/\'\s*union/i',
        '/union(\s+)all(\s+)select/i',
        '/union(\s+)select/i',

        // Stored procedure attacks
        '/exec(\s|\+)+(s|x)p\w+/i',

        // Dangerous SQL commands with specific context
        '/;\s*insert(\s+)into/i',
        '/;\s*delete(\s+)from/i',
        '/;\s*drop(\s+)(table|database|index|view|trigger|procedure|function)/i',
        '/;\s*update(\s+)\w+(\s+)set/i',
        '/;\s*truncate(\s+)table/i',
        '/;\s*alter(\s+)table/i',
        '/;\s*create(\s+)(table|database|index|view|trigger|procedure|function)/i',

        // Information gathering
        '/information_schema/i',
        '/sys\.(columns|tables|databases)/i',

        // Time-based attacks
        '/sleep\s*\(\s*\d+\s*\)/i',
        '/benchmark\s*\(/i',
        '/waitfor\s+delay/i',

        // Stacked queries
        '/;\s*(select|insert|update|delete|drop|create|alter)\s/i',
    ];

    /**
     * Fields to skip (passwords might contain special chars).
     */
    protected array $skipFields = [
        'password',
        'password_confirmation',
        'content',      // WYSIWYG article content
        'body',         // Alternative content field
        'bio',          // User bio
        'excerpt',      // Article excerpt
        'description',  // General descriptions
        'message',      // Messages/comments
        'comment',      // Comment content
        'title',        // Titles may have quotes
        'name',         // Category/tag names
        'slug',         // URL slugs
        'meta_title',   // SEO meta title
        'meta_description', // SEO meta description
        'full_name',    // User full name
    ];

    /**
     * Handle SQL injection detection.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        if (Cache::has('banned_ip_' . $ip)) {
            return response()->json([
                'success' => false,
                'message' => 'Request ditolak. IP Anda telah diblokir sementara karena aktivitas mencurigakan.'
            ], 403);
        }

        $input = $request->except($this->skipFields);

        if ($this->detectSQLInjection($input, $request)) {
            $this->logAttack($request, $input);

            return response()->json([
                'success' => false,
                'message' => 'Request tidak valid terdeteksi.'
            ], 403);
        }

        return $next($request);
    }

    /**
     * Detect SQL injection patterns.
     */
    protected function detectSQLInjection(array $input, Request $request): bool
    {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                if ($this->detectSQLInjection($value, $request)) {
                    return true;
                }
            } elseif (is_string($value)) {
                foreach ($this->patterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        Log::channel('security')->warning('SQL Injection pattern detected', [
                            'pattern' => $pattern,
                            'field' => $key,
                            'value' => substr($value, 0, 200),
                            'ip' => $request->ip(),
                        ]);
                        return true;
                    }
                }
            }
        }

        // Also check URL parameters
        $url = urldecode($request->fullUrl());
        foreach ($this->patterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log the attack attempt.
     */
    protected function logAttack(Request $request, array $input): void
    {
        $ip = $request->ip();

        $payload = [
            'type' => 'SQL Injection',
            'ip' => $ip,
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => auth()->id(),
            'input' => array_map(fn($v) => is_string($v) ? substr($v, 0, 100) : $v, $input),
            'timestamp' => now()->toDateTimeString(),
        ];

        Log::channel('security')->critical('SQL Injection attempt blocked', $payload);

        // Auto-Ban checking (5 strikes per minute)
        $strikeKey = 'security_strikes_' . $ip;
        RateLimiter::hit($strikeKey, 60);

        if (RateLimiter::tooManyAttempts($strikeKey, 5)) {
            Cache::put('banned_ip_' . $ip, true, now()->addHours(24));
            $payload['banned'] = true;
            $payload['ban_duration'] = '24 hours';
            Log::channel('security')->critical('IP Banned for 24 hours: ' . $ip);
        }

        dispatch(new SendSecurityWebhook($payload));
    }
}
