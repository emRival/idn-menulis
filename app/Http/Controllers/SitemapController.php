<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Display sitemap index
     */
    public function index()
    {
        $content = Cache::remember('sitemap_index', now()->addHours(6), function () {
            $sitemaps = [];

            // Main sitemaps
            $sitemaps[] = [
                'loc' => route('sitemap.pages'),
                'lastmod' => now()->toW3cString(),
            ];

            $sitemaps[] = [
                'loc' => route('sitemap.articles'),
                'lastmod' => Article::where('status', 'published')->latest('updated_at')->value('updated_at')?->toW3cString() ?? now()->toW3cString(),
            ];

            $sitemaps[] = [
                'loc' => route('sitemap.categories'),
                'lastmod' => Category::latest('updated_at')->value('updated_at')?->toW3cString() ?? now()->toW3cString(),
            ];

            // Image sitemap
            if (Article::where('status', 'published')->whereNotNull('featured_image')->exists()) {
                $sitemaps[] = [
                    'loc' => route('sitemap.images'),
                    'lastmod' => now()->toW3cString(),
                ];
            }

            return view('sitemap.index', compact('sitemaps'))->render();
        });

        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Static pages sitemap
     */
    public function pages()
    {
        $content = Cache::remember('sitemap_pages', now()->addDay(), function () {
            $pages = [
                [
                    'loc' => url('/'),
                    'lastmod' => now()->toW3cString(),
                    'changefreq' => config('seo.sitemap.changefreq.home', 'daily'),
                    'priority' => config('seo.sitemap.priorities.home', 1.0),
                ],
                [
                    'loc' => route('articles.index'),
                    'lastmod' => Article::where('status', 'published')->latest('updated_at')->value('updated_at')?->toW3cString() ?? now()->toW3cString(),
                    'changefreq' => 'daily',
                    'priority' => 0.9,
                ],
                [
                    'loc' => route('categories.index'),
                    'lastmod' => Category::latest('updated_at')->value('updated_at')?->toW3cString() ?? now()->toW3cString(),
                    'changefreq' => 'weekly',
                    'priority' => 0.8,
                ],
            ];

            // Add tag page if exists
            if (class_exists(\App\Models\Tag::class)) {
                try {
                    $pages[] = [
                        'loc' => route('tags.index'),
                        'lastmod' => now()->toW3cString(),
                        'changefreq' => 'weekly',
                        'priority' => 0.7,
                    ];
                } catch (\Exception $e) {
                    // Route doesn't exist, skip
                }
            }

            return view('sitemap.pages', compact('pages'))->render();
        });

        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Articles sitemap
     */
    public function articles()
    {
        $content = Cache::remember('sitemap_articles', now()->addHours(6), function () {
            $articles = Article::where('status', 'published')
                ->with(['category:id,slug', 'tags:id,slug', 'author:id,name'])
                ->select('id', 'title', 'slug', 'featured_image', 'created_at', 'updated_at', 'category_id', 'author_id')
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($article) {
                    return [
                        'loc' => route('articles.show', $article->slug),
                        'lastmod' => $article->updated_at->toW3cString(),
                        'changefreq' => $this->getChangeFrequency($article->updated_at),
                        'priority' => config('seo.sitemap.priorities.article', 0.7),
                        'title' => $article->title,
                        'image' => $article->featured_image,
                    ];
                });

            return view('sitemap.articles', compact('articles'))->render();
        });

        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Categories sitemap
     */
    public function categories()
    {
        $content = Cache::remember('sitemap_categories', now()->addDay(), function () {
            $categories = Category::withCount(['articles' => function ($query) {
                    $query->where('status', 'published');
                }])
                ->select('id', 'name', 'slug', 'updated_at')
                ->orderBy('articles_count', 'desc')
                ->get()
                ->map(function ($category) {
                    return [
                        'loc' => route('categories.show', $category->slug),
                        'lastmod' => $category->updated_at?->toW3cString() ?? now()->toW3cString(),
                        'changefreq' => config('seo.sitemap.changefreq.category', 'weekly'),
                        'priority' => $this->getCategoryPriority($category->articles_count),
                    ];
                });

            return view('sitemap.categories', compact('categories'))->render();
        });

        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Image sitemap
     */
    public function images()
    {
        $content = Cache::remember('sitemap_images', now()->addHours(12), function () {
            $articles = Article::where('status', 'published')
                ->whereNotNull('featured_image')
                ->select('id', 'title', 'slug', 'featured_image')
                ->orderBy('created_at', 'desc')
                ->limit(1000)
                ->get()
                ->map(function ($article) {
                    return [
                        'loc' => route('articles.show', $article->slug),
                        'image_loc' => str_starts_with($article->featured_image, 'http')
                            ? $article->featured_image
                            : url($article->featured_image),
                        'image_title' => $article->title,
                        'image_caption' => "Gambar untuk artikel: {$article->title}",
                    ];
                });

            return view('sitemap.images', compact('articles'))->render();
        });

        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * News sitemap (for Google News)
     */
    public function news()
    {
        $content = Cache::remember('sitemap_news', now()->addHours(1), function () {
            // Google News only accepts articles from last 2 days
            $articles = Article::where('status', 'published')
                ->where('created_at', '>=', now()->subDays(2))
                ->with(['category:id,name'])
                ->select('id', 'title', 'slug', 'created_at', 'category_id')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($article) {
                    return [
                        'loc' => route('articles.show', $article->slug),
                        'publication_name' => config('seo.site_name'),
                        'publication_language' => 'id',
                        'publication_date' => $article->created_at->toW3cString(),
                        'title' => $article->title,
                        'keywords' => $article->category?->name ?? '',
                    ];
                });

            return view('sitemap.news', compact('articles'))->render();
        });

        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Authors/writers sitemap
     */
    public function authors()
    {
        $content = Cache::remember('sitemap_authors', now()->addDay(), function () {
            $authors = \App\Models\User::whereHas('articles', function ($query) {
                    $query->where('status', 'published');
                })
                ->withCount(['articles' => function ($query) {
                    $query->where('status', 'published');
                }])
                ->select('id', 'name', 'updated_at')
                ->orderBy('articles_count', 'desc')
                ->limit(500)
                ->get()
                ->map(function ($author) {
                    return [
                        'loc' => route('profile.show', $author->id),
                        'lastmod' => $author->updated_at?->toW3cString() ?? now()->toW3cString(),
                        'changefreq' => 'weekly',
                        'priority' => 0.5,
                    ];
                });

            return view('sitemap.authors', compact('authors'))->render();
        });

        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Determine change frequency based on last update
     */
    protected function getChangeFrequency($updatedAt): string
    {
        $daysSinceUpdate = now()->diffInDays($updatedAt);

        if ($daysSinceUpdate < 7) {
            return 'daily';
        } elseif ($daysSinceUpdate < 30) {
            return 'weekly';
        } elseif ($daysSinceUpdate < 180) {
            return 'monthly';
        }

        return 'yearly';
    }

    /**
     * Calculate category priority based on article count
     */
    protected function getCategoryPriority(int $articleCount): float
    {
        if ($articleCount > 50) {
            return 0.9;
        } elseif ($articleCount > 20) {
            return 0.8;
        } elseif ($articleCount > 10) {
            return 0.7;
        } elseif ($articleCount > 0) {
            return 0.6;
        }

        return 0.5;
    }

    /**
     * Clear all sitemap caches
     */
    public function clearCache(): void
    {
        Cache::forget('sitemap_index');
        Cache::forget('sitemap_pages');
        Cache::forget('sitemap_articles');
        Cache::forget('sitemap_categories');
        Cache::forget('sitemap_images');
        Cache::forget('sitemap_news');
        Cache::forget('sitemap_authors');
    }
}
