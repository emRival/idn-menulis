<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class FeedController extends Controller
{
    /**
     * Generate RSS Feed
     */
    public function rss()
    {
        $content = Cache::remember('rss_feed', now()->addHours(1), function () {
            $articles = Article::where('status', 'published')
                ->with(['author:id,name', 'category:id,name'])
                ->select('id', 'title', 'slug', 'excerpt', 'content', 'featured_image', 'created_at', 'updated_at', 'author_id', 'category_id')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            return view('feeds.rss', [
                'articles' => $articles,
                'siteName' => config('seo.site_name'),
                'siteUrl' => config('seo.site_url'),
                'siteDescription' => config('seo.site_description'),
            ])->render();
        });

        return response($content, 200)
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    /**
     * Generate Atom Feed
     */
    public function atom()
    {
        $content = Cache::remember('atom_feed', now()->addHours(1), function () {
            $articles = Article::where('status', 'published')
                ->with(['author:id,name', 'category:id,name'])
                ->select('id', 'title', 'slug', 'excerpt', 'content', 'featured_image', 'created_at', 'updated_at', 'author_id', 'category_id')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            return view('feeds.atom', [
                'articles' => $articles,
                'siteName' => config('seo.site_name'),
                'siteUrl' => config('seo.site_url'),
                'siteDescription' => config('seo.site_description'),
            ])->render();
        });

        return response($content, 200)
            ->header('Content-Type', 'application/atom+xml; charset=UTF-8');
    }

    /**
     * Generate JSON Feed
     */
    public function json()
    {
        $content = Cache::remember('json_feed', now()->addHours(1), function () {
            $articles = Article::where('status', 'published')
                ->with(['author:id,name', 'category:id,name'])
                ->select('id', 'title', 'slug', 'excerpt', 'content', 'featured_image', 'created_at', 'updated_at', 'author_id', 'category_id')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            $items = $articles->map(function ($article) {
                $item = [
                    'id' => route('articles.show', $article->slug),
                    'url' => route('articles.show', $article->slug),
                    'title' => $article->title,
                    'content_html' => $article->content,
                    'summary' => $article->excerpt ?? strip_tags(substr($article->content, 0, 300)),
                    'date_published' => $article->created_at->toIso8601String(),
                    'date_modified' => $article->updated_at->toIso8601String(),
                ];

                if ($article->author) {
                    $item['author'] = [
                        'name' => $article->author->name,
                        'url' => route('profile.show', $article->author->id),
                    ];
                }

                if ($article->featured_image) {
                    $item['image'] = str_starts_with($article->featured_image, 'http')
                        ? $article->featured_image
                        : url($article->featured_image);
                }

                if ($article->category) {
                    $item['tags'] = [$article->category->name];
                }

                return $item;
            });

            return [
                'version' => 'https://jsonfeed.org/version/1.1',
                'title' => config('seo.site_name'),
                'description' => config('seo.site_description'),
                'home_page_url' => config('seo.site_url'),
                'feed_url' => route('feed.json'),
                'language' => 'id',
                'icon' => url(config('seo.logo')),
                'favicon' => url(config('seo.favicon')),
                'items' => $items,
            ];
        });

        return response()->json($content)
            ->header('Content-Type', 'application/feed+json; charset=UTF-8');
    }

    /**
     * Clear feed caches
     */
    public function clearCache(): void
    {
        Cache::forget('rss_feed');
        Cache::forget('atom_feed');
        Cache::forget('json_feed');
    }
}
