<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleApproval;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard based on user role.
     */
    public function index(): View
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        if ($user->isGuru()) {
            return $this->guruDashboard();
        }

        return $this->siswaDashboard();
    }

    /**
     * Display siswa dashboard.
     */
    private function siswaDashboard(): View
    {
        $user = Auth::user();

        // Article statistics
        $stats = [
            'total' => $user->articles()->count(),
            'draft' => $user->articles()->where('status', 'draft')->count(),
            'pending' => $user->articles()->where('status', 'pending')->count(),
            'published' => $user->articles()->where('status', 'published')
                ->where(function ($q) {
                    $q->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', now());
                })->count(),
            'scheduled' => $user->articles()->where('status', 'published')
                ->whereNotNull('scheduled_at')
                ->where('scheduled_at', '>', now())->count(),
            'revision' => $user->articles()->where('status', 'revision')->count(),
            'rejected' => $user->articles()->where('status', 'rejected')->count(),
        ];

        // Get total views, likes, comments
        $totalViews = $user->articles()->sum('views_count');
        $totalLikes = Like::whereIn('article_id', $user->articles()->pluck('id'))->count();
        $totalComments = Comment::whereIn('article_id', $user->articles()->pluck('id'))->count();
        $totalBookmarks = Bookmark::whereIn('article_id', $user->articles()->pluck('id'))->count();

        // Monthly writing progress (articles this month)
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $articlesThisMonth = $user->articles()
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();
        $monthlyTarget = 4; // Target 4 articles per month

        // Recent articles
        $recentArticles = $user->articles()
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Draft articles needing attention
        $draftArticles = $user->articles()
            ->where('status', 'draft')
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();

        // Articles needing revision
        $revisionArticles = $user->articles()
            ->where('status', 'revision')
            ->with('approvals.reviewer')
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();

        // Unread notifications
        $unreadNotifications = $user->notifications()
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Top performing articles
        $topArticles = $user->articles()
            ->where('status', 'published')
            ->orderBy('views_count', 'desc')
            ->limit(3)
            ->get();

        // Writing streak (consecutive days with at least 1 article)
        $writingStreak = $this->calculateWritingStreak($user);

        // Recommended articles (from other authors)
        $recommendedArticles = Article::published()
            ->where('user_id', '!=', $user->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('dashboard.siswa', compact(
            'stats', 'totalViews', 'totalLikes', 'totalComments', 'totalBookmarks',
            'recentArticles', 'draftArticles', 'revisionArticles', 'unreadNotifications',
            'topArticles', 'articlesThisMonth', 'monthlyTarget', 'writingStreak',
            'recommendedArticles'
        ));
    }

    /**
     * Calculate writing streak for user.
     */
    private function calculateWritingStreak(User $user): int
    {
        $streak = 0;
        $date = now()->startOfDay();

        while (true) {
            $hasArticle = $user->articles()
                ->whereDate('created_at', $date)
                ->exists();

            if (!$hasArticle) {
                break;
            }

            $streak++;
            $date = $date->subDay();

            if ($streak > 30) break; // Max 30 days
        }

        return $streak;
    }

    /**
     * Display guru dashboard.
     */
    private function guruDashboard(): View
    {
        $user = Auth::user();

        // Get pending approvals
        $pendingArticles = Article::where('status', 'pending')
            ->with(['user', 'category'])
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        // Articles needing revision
        $revisionArticles = Article::where('status', 'revision')
            ->with(['user', 'category'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Top authors (siswa)
        $topAuthors = User::where('role', 'siswa')
            ->where('is_active', true)
            ->withCount(['articles' => fn($q) => $q->where('status', 'published')])
            ->orderBy('articles_count', 'desc')
            ->limit(10)
            ->get();

        // Recent approvals done by this guru
        $recentApprovals = ArticleApproval::where('reviewer_id', $user->id)
            ->with(['article.user'])
            ->orderBy('reviewed_at', 'desc')
            ->limit(10)
            ->get();

        // Statistics
        $stats = [
            'pending' => Article::where('status', 'pending')->count(),
            'revision' => Article::where('status', 'revision')->count(),
            'published_today' => Article::where('status', 'published')
                ->whereDate('published_at', today())
                ->count(),
            'total_approved' => ArticleApproval::where('reviewer_id', $user->id)
                ->where('new_status', 'published')
                ->count(),
            'total_rejected' => ArticleApproval::where('reviewer_id', $user->id)
                ->where('new_status', 'rejected')
                ->count(),
            'active_siswa' => User::where('role', 'siswa')
                ->where('is_active', true)
                ->count(),
        ];

        // Weekly review stats
        $weeklyStats = ArticleApproval::where('reviewer_id', $user->id)
            ->where('reviewed_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(reviewed_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Category distribution of pending articles
        $pendingByCategory = Article::where('status', 'pending')
            ->select('category_id', DB::raw('COUNT(*) as count'))
            ->with('category')
            ->groupBy('category_id')
            ->get();

        return view('dashboard.guru', compact(
            'pendingArticles', 'revisionArticles', 'topAuthors', 'recentApprovals',
            'stats', 'weeklyStats', 'pendingByCategory'
        ));
    }

    /**
     * Display admin dashboard.
     */
    private function adminDashboard(): View
    {
        // User Statistics
        $userStats = [
            'total' => User::count(),
            'siswa' => User::where('role', 'siswa')->count(),
            'guru' => User::where('role', 'guru')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'active' => User::where('is_active', true)->count(),
            'pending' => User::whereNull('email_verified_at')->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        // Article Statistics
        $articleStats = [
            'total' => Article::count(),
            'published' => Article::where('status', 'published')->count(),
            'pending' => Article::where('status', 'pending')->count(),
            'draft' => Article::where('status', 'draft')->count(),
            'rejected' => Article::where('status', 'rejected')->count(),
            'total_views' => Article::sum('views_count'),
        ];

        // Engagement Statistics
        $engagementStats = [
            'total_comments' => Comment::count(),
            'total_likes' => Like::count(),
            'total_bookmarks' => Bookmark::count(),
            'comments_today' => Comment::whereDate('created_at', today())->count(),
        ];

        // Category Statistics
        $categories = Category::withCount(['articles' => fn($q) => $q->where('status', 'published')])
            ->orderBy('articles_count', 'desc')
            ->get();

        // Monthly article trend (last 6 months)
        $monthlyArticles = Article::where('status', 'published')
            ->where('published_at', '>=', now()->subMonths(6))
            ->selectRaw('MONTH(published_at) as month, YEAR(published_at) as year, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Monthly user registration trend
        $monthlyUsers = User::where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Recent activities
        $recentActivities = \App\Models\ActivityLog::orderBy('created_at', 'desc')
            ->with('user')
            ->limit(15)
            ->get();

        // Recent users
        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Top authors
        $topAuthors = User::withCount(['articles' => fn($q) => $q->where('status', 'published')])
            ->orderBy('articles_count', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact(
            'userStats', 'articleStats', 'engagementStats', 'categories',
            'monthlyArticles', 'monthlyUsers', 'recentActivities', 'recentUsers', 'topAuthors'
        ));
    }
}
