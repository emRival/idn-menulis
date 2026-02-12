<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index(): View
    {
        // Get featured articles
        $featured = Article::published()
            ->where('is_featured', true)
            ->with(['user', 'category'])
            ->withCount('likedBy')
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        // Get latest articles
        $latest = Article::published()
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        // Get all categories with published article count
        $categories = Category::where('is_active', true)
            ->withCount(['articles' => fn($q) => $q->where('status', 'published')])
            ->orderBy('order_position')
            ->get();

        // Get popular articles (by views)
        $popular = Article::published()
            ->with(['user', 'category'])
            ->orderBy('views_count', 'desc')
            ->limit(5)
            ->get();

        // Get popular tags
        $tags = Tag::orderBy('usage_count', 'desc')
            ->limit(10)
            ->get();

        // Get top writers
        $topWriters = \App\Models\User::withCount(['articles' => fn($q) => $q->where('status', 'published')])
            ->orderBy('articles_count', 'desc')
            ->take(5)
            ->get();

        return view('home.index', compact('featured', 'latest', 'categories', 'popular', 'tags', 'topWriters'));
    }

    /**
     * Display articles by category.
     */
    public function category(Category $category): View
    {
        $articles = $category->articles()
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        $categories = Category::where('is_active', true)->get();

        return view('articles.category', compact('category', 'articles', 'categories'));
    }

    /**
     * Display articles by tag.
     */
    public function tag(Tag $tag): View
    {
        $articles = $tag->articles()
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        $tags = Tag::orderBy('usage_count', 'desc')->limit(10)->get();

        return view('articles.tag', compact('tag', 'articles', 'tags'));
    }

    /**
     * Search articles.
     */
    public function search(Request $request): View
    {
        $query = $request->get('q', '');
        $category = $request->get('category');
        $author = $request->get('author');
        $sort = $request->get('sort', 'latest');

        $articles = Article::published();

        // Search by keyword
        if ($query) {
            $articles->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%")
                    ->orWhere('excerpt', 'like', "%{$query}%");
            });
        }

        // Filter by category
        if ($category) {
            $articles->where('category_id', $category);
        }

        // Filter by author
        if ($author) {
            $articles->where('user_id', $author);
        }

        // Sort
        match ($sort) {
            'oldest' => $articles->orderBy('published_at', 'asc'),
            'most_viewed' => $articles->orderBy('views_count', 'desc'),
            'most_liked' => $articles->withCount('likedBy')->orderBy('liked_by_count', 'desc'),
            default => $articles->orderBy('published_at', 'desc'),
        };

        $articles = $articles->paginate(20);
        $categories = Category::where('is_active', true)->get();

        return view('articles.search', compact('articles', 'query', 'categories'));
    }
}
