<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class SEOService
{
    protected array $meta = [];
    protected array $openGraph = [];
    protected array $twitter = [];
    protected array $schemas = [];
    protected ?string $canonical = null;
    protected array $alternates = [];
    protected array $preload = [];
    protected array $breadcrumbs = [];

    /**
     * Initialize with defaults
     */
    public function __construct()
    {
        $this->setDefaults();
    }

    /**
     * Set default meta tags
     */
    protected function setDefaults(): void
    {
        $this->meta = [
            'title' => config('seo.site_name'),
            'description' => config('seo.site_description'),
            'keywords' => config('seo.site_keywords'),
            'author' => config('seo.site_author'),
            'robots' => 'index, follow',
            'viewport' => 'width=device-width, initial-scale=1.0',
            'format-detection' => 'telephone=no',
        ];

        $this->openGraph = [
            'type' => 'website',
            'site_name' => config('seo.site_name'),
            'locale' => 'id_ID',
        ];

        $this->twitter = [
            'card' => 'summary_large_image',
            'site' => config('seo.social.twitter_site'),
        ];
    }

    /**
     * Set page title with site name
     */
    public function setTitle(string $title, bool $includeSiteName = true): self
    {
        $fullTitle = $includeSiteName
            ? $this->optimizeTitle($title) . ' | ' . config('seo.site_name')
            : $this->optimizeTitle($title);

        $this->meta['title'] = Str::limit($fullTitle, config('seo.content.meta_title_max', 60), '');
        $this->openGraph['title'] = $title;
        $this->twitter['title'] = $title;

        return $this;
    }

    /**
     * Optimize title for SEO
     */
    protected function optimizeTitle(string $title): string
    {
        // Remove extra whitespace
        $title = preg_replace('/\s+/', ' ', trim($title));

        // Capitalize first letter of each word for Indonesian
        $title = ucwords(strtolower($title));

        return $title;
    }

    /**
     * Set meta description
     */
    public function setDescription(string $description): self
    {
        $optimized = $this->optimizeDescription($description);

        $this->meta['description'] = $optimized;
        $this->openGraph['description'] = $optimized;
        $this->twitter['description'] = $optimized;

        return $this;
    }

    /**
     * Optimize description for SEO
     */
    protected function optimizeDescription(string $description): string
    {
        // Strip HTML tags
        $description = strip_tags($description);

        // Remove extra whitespace
        $description = preg_replace('/\s+/', ' ', trim($description));

        // Limit to optimal length
        $maxLength = config('seo.content.meta_description_max', 160);

        if (strlen($description) > $maxLength) {
            // Cut at word boundary
            $description = Str::words($description, 25, '...');

            // Ensure it ends properly
            if (strlen($description) > $maxLength) {
                $description = Str::limit($description, $maxLength - 3, '...');
            }
        }

        return $description;
    }

    /**
     * Set keywords
     */
    public function setKeywords(array|string $keywords): self
    {
        if (is_array($keywords)) {
            $keywords = implode(', ', $keywords);
        }

        $this->meta['keywords'] = $keywords;

        return $this;
    }

    /**
     * Set canonical URL
     */
    public function setCanonical(?string $url = null): self
    {
        $this->canonical = $url ?? URL::current();
        $this->openGraph['url'] = $this->canonical;

        return $this;
    }

    /**
     * Set robots meta
     */
    public function setRobots(string $robots): self
    {
        $this->meta['robots'] = $robots;
        return $this;
    }

    /**
     * Set noindex
     */
    public function noIndex(): self
    {
        $this->meta['robots'] = 'noindex, nofollow';
        return $this;
    }

    /**
     * Set featured image
     */
    public function setImage(string $image, int $width = 1200, int $height = 630): self
    {
        $imageUrl = str_starts_with($image, 'http') ? $image : url($image);

        $this->openGraph['image'] = $imageUrl;
        $this->openGraph['image:width'] = $width;
        $this->openGraph['image:height'] = $height;
        $this->openGraph['image:type'] = 'image/jpeg';
        $this->openGraph['image:alt'] = $this->meta['title'] ?? '';

        $this->twitter['image'] = $imageUrl;
        $this->twitter['image:alt'] = $this->meta['title'] ?? '';

        return $this;
    }

    /**
     * Set Open Graph type
     */
    public function setType(string $type): self
    {
        $this->openGraph['type'] = $type;
        return $this;
    }

    /**
     * Set author
     */
    public function setAuthor(string $author): self
    {
        $this->meta['author'] = $author;
        return $this;
    }

    /**
     * Set article-specific Open Graph data
     */
    public function setArticle(array $data): self
    {
        $this->openGraph['type'] = 'article';

        if (isset($data['published_time'])) {
            $this->openGraph['article:published_time'] = $data['published_time'];
        }
        if (isset($data['modified_time'])) {
            $this->openGraph['article:modified_time'] = $data['modified_time'];
        }
        if (isset($data['author'])) {
            $this->openGraph['article:author'] = $data['author'];
        }
        if (isset($data['section'])) {
            $this->openGraph['article:section'] = $data['section'];
        }
        if (isset($data['tag'])) {
            $this->openGraph['article:tag'] = is_array($data['tag'])
                ? implode(',', $data['tag'])
                : $data['tag'];
        }

        return $this;
    }

    /**
     * Add breadcrumb item
     */
    public function addBreadcrumb(string $name, ?string $url = null): self
    {
        $this->breadcrumbs[] = [
            'name' => $name,
            'url' => $url,
        ];

        return $this;
    }

    /**
     * Set breadcrumbs array
     */
    public function setBreadcrumbs(array $breadcrumbs): self
    {
        $this->breadcrumbs = $breadcrumbs;
        return $this;
    }

    /**
     * Add preload resource
     */
    public function addPreload(string $href, string $as, ?string $type = null): self
    {
        $this->preload[] = [
            'href' => $href,
            'as' => $as,
            'type' => $type,
        ];

        return $this;
    }

    /**
     * Add alternate language
     */
    public function addAlternate(string $hreflang, string $href): self
    {
        $this->alternates[] = [
            'hreflang' => $hreflang,
            'href' => $href,
        ];

        return $this;
    }

    /**
     * Add JSON-LD schema
     */
    public function addSchema(array $schema): self
    {
        $this->schemas[] = $schema;
        return $this;
    }

    /**
     * Generate WebSite schema
     */
    public function generateWebsiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => config('seo.site_name'),
            'url' => config('seo.site_url'),
            'description' => config('seo.site_description'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => config('seo.site_url') . '/search?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Generate Organization schema
     */
    public function generateOrganizationSchema(): array
    {
        $org = config('seo.organization');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $org['name'],
            'url' => config('seo.site_url'),
            'logo' => url(config('seo.logo')),
        ];

        if (!empty($org['legal_name'])) {
            $schema['legalName'] = $org['legal_name'];
        }

        if (!empty($org['founding_date'])) {
            $schema['foundingDate'] = $org['founding_date'];
        }

        // Social profiles
        $sameAs = [];
        foreach (config('seo.social') as $platform => $url) {
            if (!empty($url) && !str_starts_with($url, '@')) {
                $sameAs[] = $url;
            }
        }
        if (!empty($sameAs)) {
            $schema['sameAs'] = $sameAs;
        }

        // Contact
        if (!empty($org['contact']['email'])) {
            $schema['contactPoint'] = [
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'email' => $org['contact']['email'],
            ];

            if (!empty($org['contact']['phone'])) {
                $schema['contactPoint']['telephone'] = $org['contact']['phone'];
            }
        }

        return $schema;
    }

    /**
     * Generate Article schema
     */
    public function generateArticleSchema(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'url' => $data['url'] ?? URL::current(),
            'datePublished' => $data['published_at'] ?? now()->toIso8601String(),
            'dateModified' => $data['updated_at'] ?? now()->toIso8601String(),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $data['url'] ?? URL::current(),
            ],
            'author' => [
                '@type' => 'Person',
                'name' => $data['author']['name'] ?? config('seo.site_author'),
                'url' => $data['author']['url'] ?? config('seo.site_url'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('seo.site_name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => url(config('seo.logo')),
                ],
            ],
        ];

        if (!empty($data['image'])) {
            $schema['image'] = [
                '@type' => 'ImageObject',
                'url' => str_starts_with($data['image'], 'http') ? $data['image'] : url($data['image']),
                'width' => $data['image_width'] ?? 1200,
                'height' => $data['image_height'] ?? 630,
            ];
        }

        if (!empty($data['category'])) {
            $schema['articleSection'] = $data['category'];
        }

        if (!empty($data['tags'])) {
            $schema['keywords'] = is_array($data['tags']) ? implode(', ', $data['tags']) : $data['tags'];
        }

        if (!empty($data['word_count'])) {
            $schema['wordCount'] = $data['word_count'];
        }

        return $schema;
    }

    /**
     * Generate Breadcrumb schema
     */
    public function generateBreadcrumbSchema(): array
    {
        if (empty($this->breadcrumbs)) {
            return [];
        }

        $items = [];
        foreach ($this->breadcrumbs as $index => $breadcrumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => $breadcrumb['url'] ?? null,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /**
     * Generate FAQ schema
     */
    public function generateFAQSchema(array $faqs): array
    {
        $items = [];
        foreach ($faqs as $faq) {
            $items[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $items,
        ];
    }

    /**
     * Generate HowTo schema
     */
    public function generateHowToSchema(array $data): array
    {
        $steps = [];
        foreach ($data['steps'] as $index => $step) {
            $stepData = [
                '@type' => 'HowToStep',
                'position' => $index + 1,
                'name' => $step['name'],
                'text' => $step['text'],
            ];

            if (!empty($step['image'])) {
                $stepData['image'] = $step['image'];
            }

            $steps[] = $stepData;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'step' => $steps,
        ];

        if (!empty($data['total_time'])) {
            $schema['totalTime'] = $data['total_time'];
        }

        if (!empty($data['image'])) {
            $schema['image'] = $data['image'];
        }

        return $schema;
    }

    /**
     * Generate Person schema for author page
     */
    public function generatePersonSchema(array $data): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $data['name'],
            'url' => $data['url'] ?? '',
            'description' => $data['bio'] ?? '',
            'image' => $data['avatar'] ?? '',
            'jobTitle' => $data['job_title'] ?? 'Penulis',
            'sameAs' => $data['social_links'] ?? [],
        ];
    }

    /**
     * Render all meta tags as HTML
     */
    public function render(): string
    {
        $html = [];

        // Basic meta tags
        if (isset($this->meta['title'])) {
            $html[] = '<title>' . e($this->meta['title']) . '</title>';
        }

        foreach ($this->meta as $name => $content) {
            if ($name !== 'title' && !empty($content)) {
                $html[] = '<meta name="' . e($name) . '" content="' . e($content) . '">';
            }
        }

        // Canonical URL
        if ($this->canonical) {
            $html[] = '<link rel="canonical" href="' . e($this->canonical) . '">';
        }

        // Open Graph
        foreach ($this->openGraph as $property => $content) {
            if (!empty($content)) {
                $html[] = '<meta property="og:' . e($property) . '" content="' . e($content) . '">';
            }
        }

        // Twitter Cards
        foreach ($this->twitter as $name => $content) {
            if (!empty($content)) {
                $html[] = '<meta name="twitter:' . e($name) . '" content="' . e($content) . '">';
            }
        }

        // Alternates
        foreach ($this->alternates as $alternate) {
            $html[] = '<link rel="alternate" hreflang="' . e($alternate['hreflang']) . '" href="' . e($alternate['href']) . '">';
        }

        // Preload resources
        foreach ($this->preload as $resource) {
            $type = $resource['type'] ? ' type="' . e($resource['type']) . '"' : '';
            $html[] = '<link rel="preload" href="' . e($resource['href']) . '" as="' . e($resource['as']) . '"' . $type . '>';
        }

        // Verification codes
        if ($google = config('seo.verification.google')) {
            $html[] = '<meta name="google-site-verification" content="' . e($google) . '">';
        }
        if ($bing = config('seo.verification.bing')) {
            $html[] = '<meta name="msvalidate.01" content="' . e($bing) . '">';
        }

        // Structured data
        $schemas = $this->schemas;

        // Add breadcrumb schema if exists
        $breadcrumbSchema = $this->generateBreadcrumbSchema();
        if (!empty($breadcrumbSchema)) {
            $schemas[] = $breadcrumbSchema;
        }

        foreach ($schemas as $schema) {
            $html[] = '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
        }

        return implode("\n    ", $html);
    }

    /**
     * Generate optimized SEO for an article
     */
    public function forArticle($article, ?string $category = null, array $tags = []): self
    {
        $this->setTitle($article->title)
             ->setDescription($article->excerpt ?? $this->generateExcerpt($article->content))
             ->setCanonical(route('articles.show', $article->slug))
             ->setImage($article->featured_image ?? config('seo.default_image'))
             ->setType('article')
             ->setAuthor($article->author->name ?? config('seo.site_author'))
             ->setKeywords($tags);

        // Set article OG data
        $this->setArticle([
            'published_time' => $article->created_at->toIso8601String(),
            'modified_time' => $article->updated_at->toIso8601String(),
            'author' => $article->author->name ?? config('seo.site_author'),
            'section' => $category,
            'tag' => $tags,
        ]);

        // Add article schema
        $this->addSchema($this->generateArticleSchema([
            'title' => $article->title,
            'description' => $article->excerpt ?? $this->generateExcerpt($article->content),
            'url' => route('articles.show', $article->slug),
            'image' => $article->featured_image ?? config('seo.default_image'),
            'published_at' => $article->created_at->toIso8601String(),
            'updated_at' => $article->updated_at->toIso8601String(),
            'author' => [
                'name' => $article->author->name ?? config('seo.site_author'),
                'url' => isset($article->author) ? route('profile.show', $article->author->id) : config('seo.site_url'),
            ],
            'category' => $category,
            'tags' => $tags,
            'word_count' => str_word_count(strip_tags($article->content)),
        ]));

        // Add breadcrumbs
        $this->addBreadcrumb('Home', url('/'))
             ->addBreadcrumb($category ?? 'Artikel', $category ? route('categories.show', Str::slug($category)) : route('articles.index'))
             ->addBreadcrumb($article->title);

        return $this;
    }

    /**
     * Generate optimized SEO for a category page
     */
    public function forCategory($category, array $articles = []): self
    {
        $description = $category->description ?? "Baca artikel terbaik tentang {$category->name} di " . config('seo.site_name');

        $this->setTitle($category->name . ' - Kategori')
             ->setDescription($description)
             ->setCanonical(route('categories.show', $category->slug))
             ->setImage($category->image ?? config('seo.default_image'))
             ->setType('website');

        // Add breadcrumbs
        $this->addBreadcrumb('Home', url('/'))
             ->addBreadcrumb('Kategori', route('categories.index'))
             ->addBreadcrumb($category->name);

        // Add collection page schema
        $this->addSchema([
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $category->name,
            'description' => $description,
            'url' => route('categories.show', $category->slug),
        ]);

        return $this;
    }

    /**
     * Generate optimized SEO for home page
     */
    public function forHomePage(): self
    {
        $this->setTitle(config('seo.site_name'), false)
             ->setDescription(config('seo.site_description'))
             ->setCanonical(url('/'))
             ->setImage(config('seo.default_image'))
             ->setType('website');

        // Add website schema
        $this->addSchema($this->generateWebsiteSchema());

        // Add organization schema
        $this->addSchema($this->generateOrganizationSchema());

        return $this;
    }

    /**
     * Generate optimized SEO for author page
     */
    public function forAuthor($user): self
    {
        $name = $user->name;
        $bio = $user->bio ?? "Penulis di " . config('seo.site_name');

        $this->setTitle("Profil {$name}")
             ->setDescription(Str::limit($bio, 155))
             ->setCanonical(route('profile.show', $user->id))
             ->setImage($user->avatar ?? config('seo.default_image'))
             ->setType('profile');

        // Add breadcrumbs
        $this->addBreadcrumb('Home', url('/'))
             ->addBreadcrumb('Penulis', route('writers.index'))
             ->addBreadcrumb($name);

        // Add person schema
        $this->addSchema($this->generatePersonSchema([
            'name' => $name,
            'url' => route('profile.show', $user->id),
            'bio' => $bio,
            'avatar' => $user->avatar ?? '',
            'social_links' => collect([
                $user->twitter_url ?? null,
                $user->facebook_url ?? null,
                $user->instagram_url ?? null,
            ])->filter()->values()->toArray(),
        ]));

        return $this;
    }

    /**
     * Generate excerpt from content
     */
    protected function generateExcerpt(string $content, int $limit = 155): string
    {
        $text = strip_tags($content);
        $text = preg_replace('/\s+/', ' ', trim($text));

        if (strlen($text) <= $limit) {
            return $text;
        }

        return Str::limit($text, $limit, '...');
    }

    /**
     * Analyze content for SEO
     */
    public function analyzeContent(string $content, string $keyword = ''): array
    {
        $text = strip_tags($content);
        $wordCount = str_word_count($text);
        $charCount = strlen($text);

        $analysis = [
            'word_count' => $wordCount,
            'character_count' => $charCount,
            'reading_time' => ceil($wordCount / 200), // minutes
            'scores' => [],
            'suggestions' => [],
        ];

        // Check word count
        $minWords = config('seo.content.min_word_count', 300);
        $optimalWords = config('seo.content.optimal_word_count', 800);

        if ($wordCount < $minWords) {
            $analysis['scores']['word_count'] = 'poor';
            $analysis['suggestions'][] = "Tambahkan lebih banyak konten. Minimal {$minWords} kata untuk SEO yang baik.";
        } elseif ($wordCount < $optimalWords) {
            $analysis['scores']['word_count'] = 'moderate';
            $analysis['suggestions'][] = "Konten sudah cukup, tetapi {$optimalWords}+ kata lebih optimal.";
        } else {
            $analysis['scores']['word_count'] = 'good';
        }

        // Check keyword density if keyword provided
        if (!empty($keyword)) {
            $keywordCount = substr_count(strtolower($text), strtolower($keyword));
            $density = ($keywordCount / $wordCount) * 100;

            $analysis['keyword_density'] = round($density, 2);

            if ($density < 0.5) {
                $analysis['scores']['keyword'] = 'poor';
                $analysis['suggestions'][] = "Keyword '{$keyword}' jarang muncul. Tingkatkan penggunaannya.";
            } elseif ($density > 2.5) {
                $analysis['scores']['keyword'] = 'over';
                $analysis['suggestions'][] = "Keyword '{$keyword}' terlalu sering muncul (keyword stuffing).";
            } else {
                $analysis['scores']['keyword'] = 'good';
            }
        }

        // Check headings
        preg_match_all('/<h[1-6][^>]*>.*?<\/h[1-6]>/i', $content, $headings);
        $headingCount = count($headings[0]);

        if ($headingCount < 2) {
            $analysis['scores']['headings'] = 'poor';
            $analysis['suggestions'][] = 'Tambahkan lebih banyak heading (H2, H3) untuk struktur yang lebih baik.';
        } else {
            $analysis['scores']['headings'] = 'good';
        }

        // Check images
        preg_match_all('/<img[^>]+>/i', $content, $images);
        $imageCount = count($images[0]);

        if ($imageCount === 0 && $wordCount > 300) {
            $analysis['scores']['images'] = 'poor';
            $analysis['suggestions'][] = 'Tambahkan gambar untuk meningkatkan engagement.';
        } else {
            $analysis['scores']['images'] = 'good';
        }

        // Check image alt tags
        if ($imageCount > 0) {
            preg_match_all('/<img[^>]+alt=["\'][^"\']+["\'][^>]*>/i', $content, $imagesWithAlt);
            $imagesWithAltCount = count($imagesWithAlt[0]);

            if ($imagesWithAltCount < $imageCount) {
                $analysis['scores']['image_alt'] = 'poor';
                $analysis['suggestions'][] = 'Beberapa gambar tidak memiliki alt text.';
            } else {
                $analysis['scores']['image_alt'] = 'good';
            }
        }

        // Check internal links
        preg_match_all('/<a[^>]+href=["\'][^"\']+["\'][^>]*>/i', $content, $links);
        $linkCount = count($links[0]);

        if ($linkCount < 2 && $wordCount > 500) {
            $analysis['scores']['links'] = 'moderate';
            $analysis['suggestions'][] = 'Tambahkan lebih banyak internal link ke artikel terkait.';
        } else {
            $analysis['scores']['links'] = 'good';
        }

        // Calculate overall score
        $scores = $analysis['scores'];
        $goodCount = count(array_filter($scores, fn($s) => $s === 'good'));
        $totalChecks = count($scores);

        $analysis['overall_score'] = $totalChecks > 0
            ? round(($goodCount / $totalChecks) * 100)
            : 0;

        return $analysis;
    }

    /**
     * Generate AI-optimized summary for featured snippets
     */
    public function generateAISummary(string $content, string $question = ''): string
    {
        $text = strip_tags($content);
        $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        if (empty($sentences)) {
            return '';
        }

        // Get first 2-3 sentences that form a complete thought
        $summary = '';
        $targetLength = config('seo.ai_search.summary_length', 150);

        foreach ($sentences as $sentence) {
            if (strlen($summary) + strlen($sentence) <= $targetLength) {
                $summary .= $sentence . ' ';
            } else {
                break;
            }
        }

        return trim($summary);
    }
}
