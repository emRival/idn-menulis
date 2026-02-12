<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class FileUploadSecurity
{
    /**
     * Handle file upload security.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasFile('file') || $request->hasFile('image') || $request->hasFile('avatar') || $request->hasFile('cover_image') || $request->hasFile('featured_image')) {
            $files = $this->getAllFiles($request);

            foreach ($files as $key => $file) {
                if (is_array($file)) {
                    foreach ($file as $f) {
                        if (!$this->validateFile($f, $request)) {
                            return response()->json([
                                'success' => false,
                                'message' => 'File tidak valid atau berbahaya terdeteksi.'
                            ], 422);
                        }
                    }
                } else {
                    if (!$this->validateFile($file, $request)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'File tidak valid atau berbahaya terdeteksi.'
                        ], 422);
                    }
                }
            }
        }

        return $next($request);
    }

    /**
     * Get all uploaded files.
     */
    protected function getAllFiles(Request $request): array
    {
        return array_filter($request->allFiles());
    }

    /**
     * Validate uploaded file.
     */
    protected function validateFile($file, Request $request): bool
    {
        if (!$file || !$file->isValid()) {
            return false;
        }

        // Check file size
        $maxSize = config('security.upload.max_size_kb', 5120) * 1024;
        if ($file->getSize() > $maxSize) {
            $this->logSecurityEvent('file_too_large', $request, [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'max_size' => $maxSize,
            ]);
            return false;
        }

        // Check extension
        $extension = strtolower($file->getClientOriginalExtension());
        $blockedExtensions = config('security.upload.blocked_extensions', []);

        if (in_array($extension, $blockedExtensions)) {
            $this->logSecurityEvent('blocked_extension', $request, [
                'filename' => $file->getClientOriginalName(),
                'extension' => $extension,
            ]);
            return false;
        }

        // Check allowed extensions
        $allowedExtensions = config('security.upload.allowed_extensions', []);
        if (!empty($allowedExtensions) && !in_array($extension, $allowedExtensions)) {
            $this->logSecurityEvent('invalid_extension', $request, [
                'filename' => $file->getClientOriginalName(),
                'extension' => $extension,
            ]);
            return false;
        }

        // Check MIME type
        $mimeType = $file->getMimeType();
        $allowedMimes = config('security.upload.allowed_mimes', []);

        if (!empty($allowedMimes) && !in_array($mimeType, $allowedMimes)) {
            $this->logSecurityEvent('invalid_mime_type', $request, [
                'filename' => $file->getClientOriginalName(),
                'mime_type' => $mimeType,
            ]);
            return false;
        }

        // Check for double extensions (e.g., file.php.jpg)
        $filename = $file->getClientOriginalName();
        if ($this->hasDoubleExtension($filename, $blockedExtensions)) {
            $this->logSecurityEvent('double_extension_attack', $request, [
                'filename' => $filename,
            ]);
            return false;
        }

        // Check file content for PHP code
        if ($this->containsPHPCode($file)) {
            $this->logSecurityEvent('php_code_in_file', $request, [
                'filename' => $filename,
            ]);
            return false;
        }

        // Verify image files are actually images
        if ($this->isImageExtension($extension)) {
            if (!$this->isValidImage($file)) {
                $this->logSecurityEvent('invalid_image_file', $request, [
                    'filename' => $filename,
                ]);
                return false;
            }
        }

        return true;
    }

    /**
     * Check for double extension attack.
     */
    protected function hasDoubleExtension(string $filename, array $blockedExtensions): bool
    {
        $parts = explode('.', $filename);

        if (count($parts) > 2) {
            foreach ($parts as $part) {
                if (in_array(strtolower($part), $blockedExtensions)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if file contains PHP code.
     */
    protected function containsPHPCode($file): bool
    {
        $content = file_get_contents($file->getRealPath());

        $phpPatterns = [
            '/<\?php/i',
            '/<\?=/i',
            '/<\?/i',
            '/<script.*language\s*=\s*["\']?php/i',
            '/<%/i',
        ];

        foreach ($phpPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if extension is image type.
     */
    protected function isImageExtension(string $extension): bool
    {
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
    }

    /**
     * Validate that file is actually an image.
     */
    protected function isValidImage($file): bool
    {
        try {
            $imageInfo = @getimagesize($file->getRealPath());
            return $imageInfo !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Log security event.
     */
    protected function logSecurityEvent(string $event, Request $request, array $context = []): void
    {
        Log::channel('security')->warning("File Upload Security: {$event}", array_merge([
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'user_id' => auth()->id(),
            'timestamp' => now()->toDateTimeString(),
        ], $context));
    }
}
