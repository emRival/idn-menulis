<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class ImageService
{
    private $manager;

    /**
     * Maximum file size in KB after compression
     */
    private int $maxSizeKB = 2048;

    public function __construct()
    {
        $this->manager = new ImageManager(new GdDriver());
    }

    /**
     * Upload and resize article image with SEO optimization and auto compression.
     */
    public function uploadArticleImage(UploadedFile $file, ?string $title = null): string
    {
        $filename = $this->generateSEOFilename($title ?? 'article', $file->getClientOriginalExtension());

        // Read file content
        $image = $this->manager->read($file);

        // Resize to max 1200x630 (standard OG image size) - maintains aspect ratio
        $image->scaleDown(width: 1200, height: 630);

        // Convert to WebP with auto quality adjustment for size limit
        $webpFilename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
        $webpPath = 'articles/' . $webpFilename;

        // Auto compress to fit within size limit
        $imageData = $this->compressToMaxSize($image, $this->maxSizeKB);

        // Store in storage/app/public
        Storage::disk('public')->put($webpPath, $imageData);

        // Also create a smaller thumbnail
        $this->createThumbnailFromImage($image, 'articles/thumbs/' . $webpFilename, 400, 225);

        return $webpPath;
    }

    /**
     * Upload user avatar with SEO optimization and auto compression.
     */
    public function uploadAvatar(UploadedFile $file, ?string $username = null): string
    {
        $filename = $this->generateSEOFilename($username ?? 'avatar', $file->getClientOriginalExtension());

        // Read file content
        $image = $this->manager->read($file);

        // Resize to square 200x200
        $image->cover(200, 200);

        // Convert to WebP with auto compression
        $webpFilename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
        $webpPath = 'avatars/' . $webpFilename;

        // Auto compress - avatars are small, use higher quality
        $imageData = $this->compressToMaxSize($image, 500); // Max 500KB for avatars

        // Store in storage/app/public
        Storage::disk('public')->put($webpPath, $imageData);

        return $webpPath;
    }

    /**
     * Compress image to fit within max size (in KB)
     * Automatically adjusts quality to meet size requirement
     */
    public function compressToMaxSize($image, int $maxSizeKB = 2048, int $minQuality = 20): string
    {
        $quality = config('seo.performance.image_quality', 85);
        $maxBytes = $maxSizeKB * 1024;

        // Try progressively lower quality until size is acceptable
        while ($quality >= $minQuality) {
            $imageData = (string) $image->toWebp(quality: $quality);

            if (strlen($imageData) <= $maxBytes) {
                return $imageData;
            }

            $quality -= 10;
        }

        // If still too large, resize the image
        $currentWidth = $image->width();
        $currentHeight = $image->height();

        while (strlen($imageData) > $maxBytes && $currentWidth > 400) {
            $currentWidth = (int) ($currentWidth * 0.8);
            $currentHeight = (int) ($currentHeight * 0.8);

            $image->scaleDown(width: $currentWidth, height: $currentHeight);
            $imageData = (string) $image->toWebp(quality: $minQuality);
        }

        return $imageData;
    }

    /**
     * Create thumbnail from already loaded image
     */
    public function createThumbnailFromImage($image, string $path, int $width = 400, int $height = 225): string
    {
        // Clone the image to avoid modifying original
        $thumb = clone $image;
        $thumb->cover($width, $height);

        Storage::disk('public')->put($path, (string) $thumb->toWebp(quality: 75));

        return $path;
    }

    /**
     * Create responsive image set for srcset
     */
    public function createResponsiveSet(UploadedFile $file, string $basePath, ?string $title = null): array
    {
        $sizes = [
            'small' => ['width' => 400, 'height' => 225],
            'medium' => ['width' => 800, 'height' => 450],
            'large' => ['width' => 1200, 'height' => 675],
            'xlarge' => ['width' => 1600, 'height' => 900],
        ];

        $paths = [];
        $baseName = $this->generateSEOFilename($title ?? 'image', 'webp', false);

        foreach ($sizes as $sizeName => $dimensions) {
            $image = $this->manager->read($file);
            $image->scaleDown(width: $dimensions['width'], height: $dimensions['height']);

            $path = "{$basePath}/{$baseName}-{$sizeName}.webp";
            $imageData = $this->compressToMaxSize($image, 500); // Max 500KB per size
            Storage::disk('public')->put($path, $imageData);

            $paths[$sizeName] = [
                'path' => $path,
                'width' => $dimensions['width'],
                'height' => $dimensions['height'],
            ];
        }

        return $paths;
    }

    /**
     * Create thumbnail from uploaded file
     */
    public function createThumbnail(UploadedFile $file, string $path, int $width = 400, int $height = 225): string
    {
        $image = $this->manager->read($file);
        $image->cover($width, $height);

        Storage::disk('public')->put($path, (string) $image->toWebp(quality: 75));

        return $path;
    }

    /**
     * Generate srcset attribute
     */
    public function generateSrcset(array $responsiveSet): string
    {
        $srcset = [];

        foreach ($responsiveSet as $size => $data) {
            $srcset[] = Storage::disk('public')->url($data['path']) . ' ' . $data['width'] . 'w';
        }

        return implode(', ', $srcset);
    }

    /**
     * Generate SEO-friendly filename
     */
    protected function generateSEOFilename(string $title, string $extension, bool $withTimestamp = true): string
    {
        // Create SEO-friendly slug
        $slug = Str::slug($title);

        // Limit slug length
        $slug = Str::limit($slug, 50, '');

        // Add timestamp for uniqueness
        if ($withTimestamp) {
            $slug .= '-' . time();
        }

        return $slug . '.' . $extension;
    }

    /**
     * Optimize existing image for SEO
     */
    public function optimizeImage(string $path): ?string
    {
        if (!Storage::disk('public')->exists($path)) {
            return null;
        }

        $content = Storage::disk('public')->get($path);
        $image = $this->manager->read($content);

        // Get file info
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $directory = pathinfo($path, PATHINFO_DIRNAME);

        // Convert to WebP
        $newPath = $directory . '/' . $filename . '.webp';
        Storage::disk('public')->put($newPath, (string) $image->toWebp(quality: config('seo.performance.image_quality', 85)));

        return $newPath;
    }

    /**
     * Generate optimized alt text from title
     */
    public function generateAltText(string $title, ?string $context = null): string
    {
        $alt = $title;

        if ($context) {
            $alt .= ' - ' . $context;
        }

        // Clean up and limit
        $alt = strip_tags($alt);
        $alt = preg_replace('/\s+/', ' ', trim($alt));
        $alt = Str::limit($alt, 125, '');

        return $alt;
    }

    /**
     * Get image dimensions
     */
    public function getImageDimensions(string $path): array
    {
        if (!Storage::disk('public')->exists($path)) {
            return ['width' => 0, 'height' => 0];
        }

        $content = Storage::disk('public')->get($path);
        $image = $this->manager->read($content);

        return [
            'width' => $image->width(),
            'height' => $image->height(),
        ];
    }

    /**
     * Delete file.
     */
    public function deleteFile(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }

    /**
     * Get image URL.
     */
    public function getUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }

    /**
     * Generate picture element HTML with WebP fallback
     */
    public function generatePictureHtml(string $path, string $alt, array $attributes = []): string
    {
        $url = $this->getUrl($path);
        $webpUrl = str_replace(['.jpg', '.jpeg', '.png', '.gif'], '.webp', $url);

        $attrString = '';
        foreach ($attributes as $key => $value) {
            $attrString .= " {$key}=\"" . e($value) . "\"";
        }

        // Add lazy loading by default
        if (!isset($attributes['loading'])) {
            $attrString .= ' loading="lazy"';
        }

        return <<<HTML
<picture>
    <source srcset="{$webpUrl}" type="image/webp">
    <img src="{$url}" alt="{$alt}"{$attrString}>
</picture>
HTML;
    }
}
