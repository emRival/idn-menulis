<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

trait SEOTrait
{
    /**
     * Generate optimized meta description
     */
    public function generateMetaDescription(?string $content = null, int $limit = 160): string
    {
        $content = $content ?? $this->content ?? $this->description ?? '';

        // Strip HTML and extra whitespace
        $clean = strip_tags($content);
        $clean = html_entity_decode($clean);
        $clean = preg_replace('/\s+/', ' ', trim($clean));

        // Get optimal length
        if (strlen($clean) <= $limit) {
            return $clean;
        }

        // Cut at word boundary
        $description = Str::words($clean, 25, '');

        // Ensure it ends with proper punctuation
        if (!preg_match('/[.!?]$/', $description)) {
            $description = rtrim($description, ',;:') . '...';
        }

        // Final length check
        if (strlen($description) > $limit) {
            $description = Str::limit($description, $limit - 3, '...');
        }

        return $description;
    }

    /**
     * Generate SEO-friendly slug
     */
    public function generateSlug(?string $title = null): string
    {
        $title = $title ?? $this->title ?? $this->name ?? '';

        // Indonesian-aware slug generation
        $slug = Str::slug($title, '-');

        // Remove common Indonesian stop words for cleaner URLs
        $stopWords = ['dan', 'atau', 'yang', 'untuk', 'dengan', 'dari', 'ke', 'di', 'pada', 'ini', 'itu'];
        $slugParts = explode('-', $slug);
        $filteredParts = array_filter($slugParts, fn($part) => !in_array($part, $stopWords) || strlen($part) > 4);

        // Ensure slug is not too long
        $slug = implode('-', array_slice($filteredParts, 0, 8));

        return $slug ?: Str::slug($title, '-');
    }

    /**
     * Generate SEO-optimized image alt text
     */
    public function optimizeImageAlt(?string $title = null, ?string $context = null): string
    {
        $title = $title ?? $this->title ?? $this->name ?? '';
        $siteName = config('seo.site_name', 'IDN Menulis');

        $alt = $title;

        if ($context) {
            $alt .= ' - ' . $context;
        }

        // Add site name for branding
        $alt .= ' | ' . $siteName;

        // Clean and limit
        $alt = strip_tags($alt);
        $alt = Str::limit($alt, 125, '');

        return $alt;
    }

    /**
     * Generate breadcrumb schema
     */
    public function generateBreadcrumbSchema(array $items): array
    {
        $listItems = [];
        foreach ($items as $index => $item) {
            $listItem = [
                "@type" => "ListItem",
                "position" => $index + 1,
                "name" => $item['name'],
            ];

            // Item is optional for the last element
            if (!empty($item['url'])) {
                $listItem["item"] = $item['url'];
            }

            $listItems[] = $listItem;
        }

        return [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => $listItems
        ];
    }

    /**
     * Generate optimized title for SEO
     */
    public function generateSEOTitle(?string $title = null, bool $includeSiteName = true): string
    {
        $title = $title ?? $this->title ?? $this->name ?? '';
        $siteName = config('seo.site_name', 'IDN Menulis');
        $maxLength = config('seo.content.meta_title_max', 60);

        // Capitalize properly
        $title = ucwords(strtolower($title));

        if ($includeSiteName) {
            $separator = ' | ';
            $availableLength = $maxLength - strlen($separator) - strlen($siteName);

            if (strlen($title) > $availableLength) {
                $title = Str::limit($title, $availableLength - 3, '...');
            }

            return $title . $separator . $siteName;
        }

        return Str::limit($title, $maxLength, '...');
    }

    /**
     * Generate keywords from content
     */
    public function generateKeywords(?string $content = null, int $limit = 10): array
    {
        $content = $content ?? $this->content ?? $this->description ?? '';

        // Strip HTML and convert to lowercase
        $text = strtolower(strip_tags($content));

        // Remove special characters
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);

        // Split into words
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Indonesian stop words
        $stopWords = [
            'dan', 'atau', 'yang', 'untuk', 'dengan', 'dari', 'ke', 'di', 'pada', 'ini',
            'itu', 'adalah', 'akan', 'ada', 'tidak', 'bisa', 'dapat', 'juga', 'sudah',
            'telah', 'harus', 'dalam', 'oleh', 'sebagai', 'karena', 'maka', 'sehingga',
            'the', 'a', 'an', 'is', 'are', 'was', 'were', 'be', 'been', 'being'
        ];

        // Filter and count words
        $wordCounts = [];
        foreach ($words as $word) {
            if (strlen($word) > 3 && !in_array($word, $stopWords)) {
                $wordCounts[$word] = ($wordCounts[$word] ?? 0) + 1;
            }
        }

        // Sort by frequency
        arsort($wordCounts);

        return array_slice(array_keys($wordCounts), 0, $limit);
    }

    /**
     * Calculate reading time
     */
    public function calculateReadingTime(?string $content = null, int $wordsPerMinute = 200): int
    {
        $content = $content ?? $this->content ?? '';
        $wordCount = str_word_count(strip_tags($content));

        return max(1, ceil($wordCount / $wordsPerMinute));
    }

    /**
     * Generate excerpt optimized for AI search
     */
    public function generateAIExcerpt(?string $content = null, int $length = 150): string
    {
        $content = $content ?? $this->content ?? '';
        $text = strip_tags($content);

        // Get first paragraph or sentences
        $paragraphs = preg_split('/\n\n+/', $text);
        $firstParagraph = trim($paragraphs[0] ?? '');

        if (strlen($firstParagraph) <= $length) {
            return $firstParagraph;
        }

        // Get complete sentences within limit
        $sentences = preg_split('/(?<=[.!?])\s+/', $firstParagraph);
        $excerpt = '';

        foreach ($sentences as $sentence) {
            if (strlen($excerpt . $sentence) <= $length) {
                $excerpt .= $sentence . ' ';
            } else {
                break;
            }
        }

        return trim($excerpt) ?: Str::limit($firstParagraph, $length, '...');
    }

    /**
     * Generate Open Graph image URL
     */
    public function getOGImageUrl(): string
    {
        // Check for featured image
        if (!empty($this->featured_image)) {
            $image = $this->featured_image;
            return str_starts_with($image, 'http') ? $image : url($image);
        }

        // Check for image field
        if (!empty($this->image)) {
            $image = $this->image;
            return str_starts_with($image, 'http') ? $image : url($image);
        }

        // Return default
        return url(config('seo.default_image', '/images/og-default.jpg'));
    }

    /**
     * Generate canonical URL
     */
    public function getCanonicalUrl(): string
    {
        if (!empty($this->canonical_url)) {
            return $this->canonical_url;
        }

        // For articles
        if (!empty($this->slug) && method_exists($this, 'getRouteKey')) {
            try {
                return route('articles.show', $this->slug);
            } catch (\Exception $e) {
                // Route doesn't exist
            }
        }

        return url()->current();
    }

    /**
     * Get structured data for article
     */
    public function getArticleSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $this->title ?? '',
            'description' => $this->generateMetaDescription(),
            'url' => $this->getCanonicalUrl(),
            'datePublished' => $this->created_at?->toIso8601String() ?? now()->toIso8601String(),
            'dateModified' => $this->updated_at?->toIso8601String() ?? now()->toIso8601String(),
            'image' => $this->getOGImageUrl(),
            'author' => [
                '@type' => 'Person',
                'name' => $this->author?->name ?? config('seo.site_author'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('seo.site_name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => url(config('seo.logo')),
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $this->getCanonicalUrl(),
            ],
            'wordCount' => str_word_count(strip_tags($this->content ?? '')),
            'inLanguage' => 'id-ID',
        ];
    }

    /**
     * Check if content meets SEO requirements
     */
    public function meetsMinimumSEORequirements(?string $content = null): array
    {
        $content = $content ?? $this->content ?? '';
        $title = $this->title ?? '';

        $issues = [];

        // Word count check
        $wordCount = str_word_count(strip_tags($content));
        $minWords = config('seo.content.min_word_count', 300);
        if ($wordCount < $minWords) {
            $issues[] = "Konten terlalu pendek ({$wordCount} kata, minimal {$minWords} kata)";
        }

        // Title length check
        $titleLength = strlen($title);
        if ($titleLength < 10) {
            $issues[] = "Judul terlalu pendek (minimal 10 karakter)";
        }
        if ($titleLength > 60) {
            $issues[] = "Judul terlalu panjang (maksimal 60 karakter)";
        }

        // Check for headings
        if (!preg_match('/<h[2-6][^>]*>/i', $content)) {
            $issues[] = "Tidak ada subheading (H2-H6)";
        }

        // Check for images
        if (!preg_match('/<img[^>]+>/i', $content) && empty($this->featured_image)) {
            $issues[] = "Tidak ada gambar";
        }

        // Check for links
        if (!preg_match('/<a[^>]+href/i', $content)) {
            $issues[] = "Tidak ada link internal/eksternal";
        }

        return [
            'passes' => empty($issues),
            'issues' => $issues,
            'word_count' => $wordCount,
            'reading_time' => $this->calculateReadingTime($content),
        ];
    }
}
