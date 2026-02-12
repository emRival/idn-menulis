<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CommandInjectionProtection
{
    /**
     * Command injection patterns.
     */
    protected array $commandPatterns = [
        // Shell metacharacters - more specific patterns
        // '/[;&|`$]/',  // Too broad, disabled
        '/\$\(/',        // Command substitution $()
        '/`[^`]+`/',     // Backtick command execution
        '/\|\s*\w+/',    // Pipe to command (e.g., | cat)
        '/;\s*\w+\s*/',  // Command chaining with semicolon
        '/&&\s*\w+/',    // AND command chaining
        '/\|\|/',        // OR command chaining
        '/\x00/',        // Null byte

        // PHP dangerous functions
        '/\beval\s*\(/i',
        '/\bexec\s*\(/i',
        '/\bsystem\s*\(/i',
        '/\bpassthru\s*\(/i',
        '/\bshell_exec\s*\(/i',
        '/\bpopen\s*\(/i',
        '/\bproc_open\s*\(/i',
        '/\bpcntl_exec\s*\(/i',
        '/\bassert\s*\(/i',
        '/\bcreate_function\s*\(/i',
        '/\bpreg_replace\s*\(.*\/e/i',

        // File operations - only when followed by (
        '/\bfile_get_contents\s*\(/i',
        '/\bfile_put_contents\s*\(/i',
        '/\bfwrite\s*\(/i',
        '/\bfopen\s*\(/i',
        '/\binclude\s*\(/i',
        '/\brequire\s*\(/i',
        '/\binclude_once\s*\(/i',
        '/\brequire_once\s*\(/i',

        // Path traversal
        '/\.\.\//',
        '/\.\.\\\\/',
        '/\.\.\%2f/i',
        '/\.\.\%5c/i',
    ];

    /**
     * SSRF patterns.
     */
    protected array $ssrfPatterns = [
        // Local/Private IP addresses
        '/^(http|https):\/\/(localhost|127\.0\.0\.1|0\.0\.0\.0)/i',
        '/^(http|https):\/\/10\.\d{1,3}\.\d{1,3}\.\d{1,3}/i',
        '/^(http|https):\/\/172\.(1[6-9]|2[0-9]|3[01])\.\d{1,3}\.\d{1,3}/i',
        '/^(http|https):\/\/192\.168\.\d{1,3}\.\d{1,3}/i',
        '/^(http|https):\/\/169\.254\.\d{1,3}\.\d{1,3}/i',
        '/^(http|https):\/\/\[::1\]/i',

        // Dangerous protocols
        '/^file:\/\//i',
        '/^gopher:\/\//i',
        '/^dict:\/\//i',
        '/^ftp:\/\//i',
        '/^tftp:\/\//i',
        '/^ldap:\/\//i',
        '/^ssh:\/\//i',
        '/^telnet:\/\//i',

        // Cloud metadata endpoints
        '/169\.254\.169\.254/i',
        '/metadata\.google\.internal/i',
        '/instance-data/i',
    ];

    /**
     * Deserialization attack patterns.
     */
    protected array $deserializationPatterns = [
        '/O:\d+:"[^"]+"/i',  // PHP serialized object
        '/a:\d+:{/i',        // PHP serialized array
        '/s:\d+:"/i',        // PHP serialized string
        '/rO0AB/i',          // Java serialized object (base64)
        '/\{"@type"/i',      // Fastjson deserialization
    ];

    /**
     * Fields to skip.
     */
    protected array $skipFields = [
        'password',
        'password_confirmation',
        '_token',
        '_method',
        'content',      // WYSIWYG article content
        'body',         // Alternative content field
        'bio',          // User bio can contain special chars
        'excerpt',      // Article excerpt
        'description',  // General descriptions
        'message',      // Messages/comments
        'comment',      // Comment content
        'name',         // Category/tag names
        'slug',         // URL slugs
        'title',        // Titles
        'meta_title',   // SEO meta title
        'meta_description', // SEO meta description
        'full_name',    // User full name
        'color',        // Category color codes
        'icon',         // Icon classes
    ];

    /**
     * Handle command injection protection.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->except($this->skipFields);

        // Check for command injection
        if ($this->detectCommandInjection($input, $request)) {
            return $this->blockRequest($request, 'Command Injection');
        }

        // Check for SSRF
        if ($this->detectSSRF($input, $request)) {
            return $this->blockRequest($request, 'SSRF');
        }

        // Check for deserialization attacks
        if ($this->detectDeserializationAttack($input, $request)) {
            return $this->blockRequest($request, 'Deserialization Attack');
        }

        return $next($request);
    }

    /**
     * Detect command injection patterns.
     */
    protected function detectCommandInjection(array $input, Request $request): bool
    {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                if ($this->detectCommandInjection($value, $request)) {
                    return true;
                }
            } elseif (is_string($value)) {
                foreach ($this->commandPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $this->logAttack($request, 'Command Injection', $key, $value, $pattern);
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Detect SSRF patterns.
     */
    protected function detectSSRF(array $input, Request $request): bool
    {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                if ($this->detectSSRF($value, $request)) {
                    return true;
                }
            } elseif (is_string($value) && filter_var($value, FILTER_VALIDATE_URL)) {
                foreach ($this->ssrfPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $this->logAttack($request, 'SSRF', $key, $value, $pattern);
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Detect deserialization attack patterns.
     */
    protected function detectDeserializationAttack(array $input, Request $request): bool
    {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                if ($this->detectDeserializationAttack($value, $request)) {
                    return true;
                }
            } elseif (is_string($value)) {
                foreach ($this->deserializationPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $this->logAttack($request, 'Deserialization', $key, $value, $pattern);
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Log attack attempt.
     */
    protected function logAttack(Request $request, string $type, string $field, string $value, string $pattern): void
    {
        Log::channel('security')->critical("{$type} attempt detected", [
            'type' => $type,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'field' => $field,
            'value' => substr($value, 0, 200),
            'pattern' => $pattern,
            'user_id' => auth()->id(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Block malicious request.
     */
    protected function blockRequest(Request $request, string $type): Response
    {
        return response()->json([
            'success' => false,
            'message' => 'Request tidak valid terdeteksi.'
        ], 403);
    }
}
