<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\SEOService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SEOAnalyzerController extends Controller
{
    protected SEOService $seoService;

    public function __construct(SEOService $seoService)
    {
        $this->seoService = $seoService;
    }

    /**
     * Show SEO dashboard
     */
    public function index()
    {
        // Get articles with SEO issues
        $articles = Article::where('status', 'published')
            ->select('id', 'title', 'slug', 'content', 'excerpt', 'featured_image', 'created_at')
            ->latest()
            ->limit(50)
            ->get()
            ->map(function ($article) {
                $analysis = $this->analyzeArticle($article);
                return [
                    'article' => $article,
                    'analysis' => $analysis,
                ];
            });

        // Summary stats
        $stats = [
            'total_articles' => Article::where('status', 'published')->count(),
            'good_seo' => $articles->filter(fn($a) => $a['analysis']['overall_score'] >= 80)->count(),
            'needs_improvement' => $articles->filter(fn($a) => $a['analysis']['overall_score'] >= 50 && $a['analysis']['overall_score'] < 80)->count(),
            'poor_seo' => $articles->filter(fn($a) => $a['analysis']['overall_score'] < 50)->count(),
        ];

        return view('admin.seo.index', compact('articles', 'stats'));
    }

    /**
     * Analyze single article
     */
    public function analyze(Article $article)
    {
        $analysis = $this->analyzeArticle($article);

        return response()->json($analysis);
    }

    /**
     * Bulk analyze articles
     */
    public function bulkAnalyze(Request $request)
    {
        $articleIds = $request->input('article_ids', []);

        $articles = Article::whereIn('id', $articleIds)->get();

        $results = $articles->map(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'analysis' => $this->analyzeArticle($article),
            ];
        });

        return response()->json($results);
    }

    /**
     * Get SEO suggestions for article
     */
    public function suggestions(Article $article)
    {
        $analysis = $this->analyzeArticle($article);

        $suggestions = [];

        // Title suggestions
        if (strlen($article->title) > 60) {
            $suggestions['title'] = [
                'issue' => 'Judul terlalu panjang',
                'current' => $article->title,
                'suggested' => Str::limit($article->title, 57, '...'),
            ];
        }

        // Meta description
        if (empty($article->excerpt) || strlen($article->excerpt) < 120) {
            $suggestions['meta_description'] = [
                'issue' => 'Meta description terlalu pendek atau tidak ada',
                'suggested' => $this->seoService->generateAISummary($article->content),
            ];
        }

        // Featured image
        if (empty($article->featured_image)) {
            $suggestions['featured_image'] = [
                'issue' => 'Tidak ada gambar utama',
                'suggestion' => 'Tambahkan gambar dengan rasio 1200x630 pixel untuk optimal sharing di social media',
            ];
        }

        // Content suggestions
        $wordCount = str_word_count(strip_tags($article->content));
        if ($wordCount < 300) {
            $suggestions['content_length'] = [
                'issue' => 'Konten terlalu pendek',
                'current' => $wordCount . ' kata',
                'suggested' => 'Minimal 300 kata, optimal 800+ kata',
            ];
        }

        // Heading structure
        if (!preg_match('/<h[2-6][^>]*>/i', $article->content)) {
            $suggestions['headings'] = [
                'issue' => 'Tidak ada subheading',
                'suggestion' => 'Tambahkan H2, H3 untuk struktur konten yang lebih baik',
            ];
        }

        return response()->json([
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
            ],
            'analysis' => $analysis,
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * Run full analysis on article
     */
    protected function analyzeArticle(Article $article): array
    {
        $content = $article->content ?? '';
        $title = $article->title ?? '';

        $analysis = [
            'overall_score' => 0,
            'scores' => [],
            'issues' => [],
            'word_count' => 0,
            'reading_time' => 0,
        ];

        // Word count
        $wordCount = str_word_count(strip_tags($content));
        $analysis['word_count'] = $wordCount;
        $analysis['reading_time'] = max(1, ceil($wordCount / 200));

        // Title analysis
        $titleLength = strlen($title);
        if ($titleLength >= 10 && $titleLength <= 60) {
            $analysis['scores']['title'] = 100;
        } elseif ($titleLength > 60) {
            $analysis['scores']['title'] = 60;
            $analysis['issues'][] = 'Judul terlalu panjang (>' . 60 . ' karakter)';
        } else {
            $analysis['scores']['title'] = 40;
            $analysis['issues'][] = 'Judul terlalu pendek';
        }

        // Content length
        $minWords = config('seo.content.min_word_count', 300);
        $optimalWords = config('seo.content.optimal_word_count', 800);

        if ($wordCount >= $optimalWords) {
            $analysis['scores']['content_length'] = 100;
        } elseif ($wordCount >= $minWords) {
            $analysis['scores']['content_length'] = 70;
        } elseif ($wordCount >= 150) {
            $analysis['scores']['content_length'] = 40;
            $analysis['issues'][] = "Konten pendek ({$wordCount} kata)";
        } else {
            $analysis['scores']['content_length'] = 20;
            $analysis['issues'][] = "Konten sangat pendek ({$wordCount} kata)";
        }

        // Meta description / Excerpt
        $excerpt = $article->excerpt ?? '';
        $excerptLength = strlen($excerpt);

        if ($excerptLength >= 120 && $excerptLength <= 160) {
            $analysis['scores']['meta_description'] = 100;
        } elseif ($excerptLength > 0) {
            $analysis['scores']['meta_description'] = 60;
            $analysis['issues'][] = 'Meta description tidak optimal';
        } else {
            $analysis['scores']['meta_description'] = 30;
            $analysis['issues'][] = 'Tidak ada meta description';
        }

        // Featured image
        if (!empty($article->featured_image)) {
            $analysis['scores']['featured_image'] = 100;
        } else {
            $analysis['scores']['featured_image'] = 0;
            $analysis['issues'][] = 'Tidak ada gambar utama';
        }

        // Headings
        preg_match_all('/<h[2-6][^>]*>/i', $content, $headings);
        $headingCount = count($headings[0]);

        if ($headingCount >= 3) {
            $analysis['scores']['headings'] = 100;
        } elseif ($headingCount >= 1) {
            $analysis['scores']['headings'] = 70;
        } else {
            $analysis['scores']['headings'] = 20;
            $analysis['issues'][] = 'Tidak ada subheading (H2-H6)';
        }

        // Internal/External links
        preg_match_all('/<a[^>]+href/i', $content, $links);
        $linkCount = count($links[0]);

        if ($linkCount >= 3) {
            $analysis['scores']['links'] = 100;
        } elseif ($linkCount >= 1) {
            $analysis['scores']['links'] = 70;
        } else {
            $analysis['scores']['links'] = 30;
            $analysis['issues'][] = 'Tidak ada link';
        }

        // Images in content
        preg_match_all('/<img[^>]+>/i', $content, $images);
        $imageCount = count($images[0]);

        if ($imageCount >= 2 || !empty($article->featured_image)) {
            $analysis['scores']['images'] = 100;
        } elseif ($imageCount >= 1) {
            $analysis['scores']['images'] = 70;
        } else {
            $analysis['scores']['images'] = 30;
        }

        // Image alt tags
        if ($imageCount > 0) {
            preg_match_all('/<img[^>]+alt=["\'][^"\']+["\'][^>]*>/i', $content, $imagesWithAlt);
            $altRatio = count($imagesWithAlt[0]) / $imageCount;

            if ($altRatio >= 1) {
                $analysis['scores']['image_alt'] = 100;
            } elseif ($altRatio >= 0.5) {
                $analysis['scores']['image_alt'] = 60;
                $analysis['issues'][] = 'Beberapa gambar tidak punya alt text';
            } else {
                $analysis['scores']['image_alt'] = 20;
                $analysis['issues'][] = 'Banyak gambar tanpa alt text';
            }
        } else {
            $analysis['scores']['image_alt'] = 50; // Neutral if no images
        }

        // Calculate overall score
        $totalScore = array_sum($analysis['scores']);
        $totalChecks = count($analysis['scores']);
        $analysis['overall_score'] = $totalChecks > 0 ? round($totalScore / $totalChecks) : 0;

        // Set grade
        if ($analysis['overall_score'] >= 80) {
            $analysis['grade'] = 'A';
            $analysis['grade_color'] = 'green';
        } elseif ($analysis['overall_score'] >= 60) {
            $analysis['grade'] = 'B';
            $analysis['grade_color'] = 'yellow';
        } elseif ($analysis['overall_score'] >= 40) {
            $analysis['grade'] = 'C';
            $analysis['grade_color'] = 'orange';
        } else {
            $analysis['grade'] = 'D';
            $analysis['grade_color'] = 'red';
        }

        return $analysis;
    }
}
