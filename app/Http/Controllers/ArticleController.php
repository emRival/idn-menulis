<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Tag;
use App\Services\ArticleService;
use App\Services\ImageService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private ArticleService $articleService,
        private ImageService $imageService
    ) {
    }

    /**
     * Display a listing of all published articles.
     */
    public function index(): View
    {
        $articles = Article::with(['user', 'category', 'tags'])
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $categories = Category::withCount(['articles' => fn($q) => $q->published()])->get();
        $tags = Tag::withCount(['articles' => fn($q) => $q->published()])
            ->orderBy('articles_count', 'desc')
            ->take(20)
            ->get();

        return view('articles.index', compact('articles', 'categories', 'tags'));
    }

    /**
     * Display the article detail page.
     */
    public function show(Article $article): View
    {
        // Check if article is published (not scheduled) or author viewing their own article
        $isOwner = Auth::check() && Auth::user()->id === $article->user_id;
        $isAdmin = Auth::check() && Auth::user()->isAdmin();

        if (!$article->isPublished() && !$isOwner && !$isAdmin) {
            abort(404);
        }

        // Increment view count for published articles
        if ($article->isPublished()) {
            $article->incrementViews();
        }

        // Get approved comments with pagination
        $comments = $article->comments()
            ->where('is_approved', true)
            ->whereNull('parent_id') // Only get top-level comments
            ->with(['user', 'replies.user', 'likes', 'reactions', 'replies.likes', 'replies.reactions'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Get related articles
        $related = Article::published()
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->limit(6)
            ->get();

        $isLiked = Auth::check() ? $article->isLikedBy(Auth::user()) : false;
        $isBookmarked = Auth::check() ? $article->isBookmarkedBy(Auth::user()) : false;
        $commentsCount = $article->allComments()->count();

        return view('articles.show', compact('article', 'comments', 'related', 'isLiked', 'isBookmarked', 'commentsCount'));
    }

    /**
     * Display the create article form.
     */
    public function create(): View
    {
        $this->authorize('create', Article::class);

        $categories = Category::where('is_active', true)->get();
        $tags = Tag::all();

        return view('articles.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created article.
     */
    public function store(ArticleRequest $request): RedirectResponse
    {
        $this->authorize('create', Article::class);

        $article = $this->articleService->createArticle($request->validated(), Auth::user());

        return redirect()->route('articles.show', $article)
            ->with('success', 'Artikel berhasil dibuat. Status: ' . ucfirst($article->status));
    }

    /**
     * Display the edit article form.
     */
    public function edit(Article $article): View
    {
        $this->authorize('update', $article);

        $categories = Category::where('is_active', true)->get();
        $tags = Tag::all();

        // Generate cover URL for JavaScript
        $coverUrl = $article->featured_image ? Storage::url($article->featured_image) : null;

        return view('articles.edit', compact('article', 'categories', 'tags', 'coverUrl'));
    }

    /**
     * Update the article.
     */
    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        $this->authorize('update', $article);

        $this->articleService->updateArticle($article, $request->validated());

        return redirect()->route('articles.show', $article)
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    /**
     * Delete the article.
     */
    public function destroy(Article $article): RedirectResponse
    {
        $this->authorize('delete', $article);

        $article->delete();

        return redirect()->route('dashboard.articles')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    /**
     * Upload featured image.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240|mimes:jpeg,png,jpg,webp', // 10MB, will be auto-compressed
        ]);

        $path = $this->imageService->uploadArticleImage($request->file('image'));

        // Return full URL for frontend use
        return response()->json(['url' => Storage::url($path)]);
    }

    /**
     * Publish article immediately.
     */
    public function publish(Article $article): RedirectResponse
    {
        $this->authorize('publish', $article);

        $this->articleService->publishArticle($article);

        return back()->with('success', 'Artikel berhasil dipublikasikan.');
    }

    /**
     * Schedule article for publishing.
     */
    public function schedule(Request $request, Article $article): RedirectResponse
    {
        $this->authorize('publish', $article);

        $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $this->articleService->scheduleArticle($article, $request->input('scheduled_at'));

        return back()->with('success', 'Artikel terjadwal untuk dipublikasikan.');
    }

    /**
     * Revert article to draft.
     */
    public function revertToDraft(Article $article): RedirectResponse
    {
        $this->authorize('update', $article);

        $article->update(['status' => 'draft']);

        return back()->with('success', 'Artikel dikembalikan ke draft.');
    }

    /**
     * Toggle comments on/off for an article (admin only).
     */
    public function toggleComments(Article $article): JsonResponse
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat mengatur komentar.',
            ], 403);
        }

        $article->update([
            'comments_enabled' => !$article->comments_enabled,
        ]);

        $status = $article->comments_enabled ? 'dibuka' : 'ditutup';

        return response()->json([
            'success' => true,
            'message' => "Komentar artikel telah {$status}.",
            'comments_enabled' => $article->comments_enabled,
        ]);
    }

    /**
     * Get trending articles for sidebar.
     */
    public function trending()
    {
        $articles = Cache::remember('trending_articles', 30 * 60, function () {
            return Article::where('status', 'published')
                ->orderBy('views_count', 'desc')
                ->limit(5)
                ->get();
        });

        return response()->json($articles);
    }

    /**
     * Dashboard: My Articles with stats and filters
     */
    public function myArticles(Request $request): View
    {
        $user = Auth::user();

        // Build query with filters
        $query = $user->articles()->with(['category', 'tags']);

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'scheduled') {
                // Scheduled articles have status 'scheduled'
                $query->where('status', 'scheduled');
            } else {
                $query->where('status', $request->status);
            }
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->input('sort', 'latest');
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $articles = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => $user->articles()->count(),
            'published' => $user->articles()->where('status', 'published')->count(),
            'scheduled' => $user->articles()->where('status', 'scheduled')->count(),
            'pending' => $user->articles()->where('status', 'pending')->count(),
            'draft' => $user->articles()->where('status', 'draft')->count(),
            'revision' => $user->articles()->where('status', 'revision')->count(),
            'rejected' => $user->articles()->where('status', 'rejected')->count(),
            'total_views' => $user->articles()->sum('views_count'),
            'total_likes' => Like::whereIn('article_id', $user->articles()->pluck('id'))->count(),
        ];

        // Categories for filter
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.my-articles', compact('articles', 'stats', 'categories'));
    }

    /**
     * Duplicate an article
     */
    public function duplicate(Article $article): RedirectResponse
    {
        $this->authorize('update', $article);

        $newArticle = $article->replicate();
        $newArticle->title = $article->title . ' (Copy)';
        $newArticle->slug = $article->slug . '-copy-' . time();
        $newArticle->status = 'draft';
        $newArticle->views_count = 0;
        $newArticle->published_at = null;
        $newArticle->save();

        // Copy tags
        $newArticle->tags()->sync($article->tags->pluck('id'));

        return redirect()->route('articles.edit', $newArticle)
            ->with('success', 'Artikel berhasil diduplikasi sebagai draft.');
    }

    /**
     * Bulk delete articles
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'article_ids' => 'required|array',
            'article_ids.*' => 'exists:articles,id',
        ]);

        $user = Auth::user();
        $deleted = 0;

        foreach ($request->article_ids as $id) {
            $article = Article::find($id);
            if ($article && $article->user_id === $user->id) {
                $article->delete();
                $deleted++;
            }
        }

        return back()->with('success', "{$deleted} artikel berhasil dihapus.");
    }

    /**
     * Bulk submit articles for review
     */
    public function bulkSubmit(Request $request): RedirectResponse
    {
        $request->validate([
            'article_ids' => 'required|array',
            'article_ids.*' => 'exists:articles,id',
        ]);

        $user = Auth::user();
        $submitted = 0;

        foreach ($request->article_ids as $id) {
            $article = Article::find($id);
            if ($article && $article->user_id === $user->id && in_array($article->status, ['draft', 'revision'])) {
                $article->update(['status' => 'pending']);
                $submitted++;
            }
        }

        return back()->with('success', "{$submitted} artikel berhasil diajukan untuk review.");
    }
}
