<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleApproval;
use App\Models\Category;
use App\Models\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    /**
     * Display articles pending approval.
     */
    public function pending(Request $request): View
    {
        if (!Auth::user()->isGuru() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $query = Article::whereIn('status', ['pending_review', 'pending', 'revision', 'rejected', 'published'])
            ->with('user', 'category');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('full_name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'pending') {
                $query->whereIn('status', ['pending_review', 'pending']);
            } else {
                $query->where('status', $status);
            }
        } else {
            // Default: show pending only
            $query->whereIn('status', ['pending_review', 'pending']);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by class
        if ($request->filled('class')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('class', $request->class);
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->get('sort', 'oldest');
        if ($sortBy === 'newest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sortBy === 'popular') {
            $query->orderBy('views_count', 'desc');
        } else {
            $query->orderBy('created_at', 'asc'); // oldest first (FIFO)
        }

        $articles = $query->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'pending' => Article::whereIn('status', ['pending_review', 'pending'])->count(),
            'approved' => Article::where('status', 'published')->count(),
            'rejected' => Article::where('status', 'rejected')->count(),
            'revision' => Article::where('status', 'revision')->count(),
        ];

        // Get categories and classes for filter
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $classes = \App\Models\User::whereNotNull('class')
            ->where('role', 'siswa')
            ->distinct()
            ->pluck('class')
            ->sort();

        return view('approvals.pending', compact('articles', 'stats', 'categories', 'classes'));
    }

    /**
     * Show approval form.
     */
    public function show(int $article): View
    {
        $article = Article::findOrFail($article);
        $this->authorize('approve', $article);

        $article->load('user', 'category', 'tags', 'approvals.reviewer');

        return view('approvals.show', compact('article'));
    }

    /**
     * Approve article.
     */
    public function approve(Request $request, int $article): JsonResponse|RedirectResponse
    {
        $article = Article::findOrFail($article);
        $this->authorize('approve', $article);

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $previousStatus = $article->status;

        $article->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Log approval
        ArticleApproval::create([
            'article_id' => $article->id,
            'reviewer_id' => Auth::id(),
            'previous_status' => $previousStatus,
            'new_status' => 'published',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Send notification to author
        Notification::create([
            'user_id' => $article->user_id,
            'type' => 'article_approved',
            'title' => 'Artikel Disetujui',
            'message' => 'Artikel "' . $article->title . '" telah disetujui dan dipublikasikan.',
            'action_url' => route('articles.show', $article),
        ]);

        return request()->wantsJson()
            ? response()->json(['success' => true, 'message' => 'Artikel berhasil disetujui.'])
            : redirect()->route('approvals.pending')->with('success', 'Artikel berhasil disetujui.');
    }

    /**
     * Request revision for article.
     */
    public function revision(Request $request, int $article): JsonResponse|RedirectResponse
    {
        $article = Article::findOrFail($article);
        $this->authorize('approve', $article);

        $validated = $request->validate([
            'revision_notes' => 'required|string|max:2000',
        ]);

        $previousStatus = $article->status;

        $article->update([
            'status' => 'revision',
            'rejection_reason' => $validated['revision_notes'],
        ]);

        // Log revision request
        ArticleApproval::create([
            'article_id' => $article->id,
            'reviewer_id' => Auth::id(),
            'previous_status' => $previousStatus,
            'new_status' => 'revision',
            'notes' => $validated['revision_notes'],
        ]);

        // Send notification to author
        Notification::create([
            'user_id' => $article->user_id,
            'type' => 'article_revision',
            'title' => 'Artikel Perlu Direvisi',
            'message' => 'Artikel "' . $article->title . '" perlu direvisi. Silakan baca catatan dari reviewer.',
            'action_url' => route('articles.edit', $article),
        ]);

        return request()->wantsJson()
            ? response()->json(['success' => true, 'message' => 'Permintaan revisi berhasil dikirim.'])
            : redirect()->route('approvals.pending')->with('success', 'Permintaan revisi berhasil dikirim.');
    }

    /**
     * Reject article.
     */
    public function reject(Request $request, int $article): JsonResponse|RedirectResponse
    {
        $article = Article::findOrFail($article);
        $this->authorize('approve', $article);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $previousStatus = $article->status;

        $article->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Log rejection
        ArticleApproval::create([
            'article_id' => $article->id,
            'reviewer_id' => Auth::id(),
            'previous_status' => $previousStatus,
            'new_status' => 'rejected',
            'notes' => $validated['rejection_reason'],
        ]);

        // Send notification to author
        Notification::create([
            'user_id' => $article->user_id,
            'type' => 'article_rejected',
            'title' => 'Artikel Ditolak',
            'message' => 'Artikel "' . $article->title . '" telah ditolak. Silakan baca keterangan penolakan dan revisi artikel Anda.',
            'action_url' => route('articles.edit', $article),
        ]);

        return request()->wantsJson()
            ? response()->json(['success' => true, 'message' => 'Artikel berhasil ditolak.'])
            : redirect()->route('approvals.pending')->with('success', 'Artikel berhasil ditolak.');
    }

    /**
     * Bulk action on articles.
     */
    public function bulkAction(Request $request): JsonResponse
    {
        if (!Auth::user()->isGuru() && !Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'action' => 'required|in:approve,reject,revision',
            'ids' => 'required|array',
            'ids.*' => 'exists:articles,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $action = $request->action;
        $ids = $request->ids;
        $notes = $request->notes ?? '';
        $count = 0;

        foreach ($ids as $id) {
            $article = Article::find($id);
            if (!$article) continue;

            $previousStatus = $article->status;
            $newStatus = match($action) {
                'approve' => 'published',
                'reject' => 'rejected',
                'revision' => 'revision',
            };

            $article->update([
                'status' => $newStatus,
                'published_at' => $action === 'approve' ? now() : $article->published_at,
                'rejection_reason' => $action !== 'approve' ? $notes : null,
            ]);

            // Log action
            ArticleApproval::create([
                'article_id' => $article->id,
                'reviewer_id' => Auth::id(),
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
                'notes' => $notes,
            ]);

            // Send notification
            $notifType = match($action) {
                'approve' => 'article_approved',
                'reject' => 'article_rejected',
                'revision' => 'article_revision',
            };
            $notifTitle = match($action) {
                'approve' => 'Artikel Disetujui',
                'reject' => 'Artikel Ditolak',
                'revision' => 'Artikel Perlu Direvisi',
            };
            $notifMessage = match($action) {
                'approve' => 'Artikel "' . $article->title . '" telah disetujui.',
                'reject' => 'Artikel "' . $article->title . '" telah ditolak.',
                'revision' => 'Artikel "' . $article->title . '" perlu direvisi.',
            };

            Notification::create([
                'user_id' => $article->user_id,
                'type' => $notifType,
                'title' => $notifTitle,
                'message' => $notifMessage,
                'action_url' => $action === 'approve' ? route('articles.show', $article) : route('articles.edit', $article),
            ]);

            $count++;
        }

        $message = match($action) {
            'approve' => "$count artikel berhasil disetujui.",
            'reject' => "$count artikel berhasil ditolak.",
            'revision' => "$count artikel berhasil diminta revisi.",
        };

        return response()->json([
            'success' => true,
            'message' => $message,
            'count' => $count,
        ]);
    }

    /**
     * Get article data for preview.
     */
    public function getData(int $article): JsonResponse
    {
        $article = Article::findOrFail($article);
        $this->authorize('review', $article);

        $article->load('user', 'category', 'tags', 'approvals.reviewer');

        return response()->json([
            'success' => true,
            'article' => $article,
        ]);
    }

    /**
     * Get approval history.
     */
    public function history(int $article): View
    {
        $article = Article::findOrFail($article);

        $approvals = $article->approvals()
            ->with('reviewer')
            ->orderBy('reviewed_at', 'desc')
            ->get();

        return view('approvals.history', compact('article', 'approvals'));
    }
}
