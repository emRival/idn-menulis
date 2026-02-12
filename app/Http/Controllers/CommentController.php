<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Article;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\CommentReaction;
use App\Models\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    /**
     * Display comments for an article.
     */
    public function index(Article $article): View
    {
        $comments = $article->allComments()
            ->with('user', 'replies.user')
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('comments.index', compact('article', 'comments'));
    }

    /**
     * Store a new comment.
     */
    public function store(CommentRequest $request, Article $article): JsonResponse
    {
        $this->authorize('create', Comment::class);

        $comment = $article->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'parent_id' => $request->input('parent_id'),
            'is_approved' => $this->shouldAutoApprove(Auth::user()),
        ]);

        // Send notification to article author if comment needs approval
        if (!$comment->is_approved && $article->user_id !== Auth::id()) {
            Notification::create([
                'user_id' => $article->user_id,
                'type' => 'comment_new',
                'title' => 'Komentar baru',
                'message' => Auth::user()->full_name . ' mengomentari artikel Anda',
                'action_url' => route('articles.show', $article),
            ]);
        }

        // Notify parent comment author if this is a reply
        if ($comment->parent_id) {
            $parentComment = Comment::find($comment->parent_id);
            if ($parentComment->user_id !== Auth::id()) {
                Notification::create([
                    'user_id' => $parentComment->user_id,
                    'type' => 'comment_reply',
                    'title' => 'Balasan komentar',
                    'message' => Auth::user()->full_name . ' membalas komentar Anda',
                    'action_url' => route('articles.show', $article),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => $comment->is_approved ? 'Komentar berhasil ditambahkan.' : 'Komentar Anda sedang menunggu persetujuan.',
            'comment' => $comment->load('user'),
        ]);
    }

    /**
     * Update a comment.
     */
    public function update(CommentRequest $request, Comment $comment): JsonResponse
    {
        $this->authorize('update', $comment);

        $comment->update(['content' => $request->input('content')]);

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil diperbarui.',
            'comment' => $comment,
        ]);
    }

    /**
     * Delete a comment.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil dihapus.',
        ]);
    }

    /**
     * Approve a comment (admin/guru only).
     */
    public function approve(Comment $comment): JsonResponse
    {
        $this->authorize('approve', $comment);

        $comment->update(['is_approved' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Komentar disetujui.',
        ]);
    }

    /**
     * Reject a comment (admin/guru only).
     */
    public function reject(Comment $comment): JsonResponse
    {
        $this->authorize('reject', $comment);

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Komentar ditolak.',
        ]);
    }

    /**
     * Admin delete a comment (marks as deleted by admin instead of removing).
     */
    public function adminDelete(Comment $comment): JsonResponse
    {
        $user = Auth::user();

        // Only admin can do this
        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk melakukan ini.',
            ], 403);
        }

        $comment->update([
            'deleted_by_admin' => true,
            'deleted_by' => $user->id,
            'content' => '[Komentar ini dihapus oleh Admin]',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Komentar telah dihapus oleh admin.',
        ]);
    }

    /**
     * Determine if comment should be auto-approved.
     */
    private function shouldAutoApprove($user): bool
    {
        return $user->isAdmin() || $user->isGuru();
    }

    /**
     * Toggle like on a comment.
     */
    public function toggleLike(Comment $comment): JsonResponse
    {
        $user = Auth::user();
        $existingLike = CommentLike::where('user_id', $user->id)
            ->where('comment_id', $comment->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $isLiked = false;
            $message = 'Like dihapus.';
        } else {
            CommentLike::create([
                'user_id' => $user->id,
                'comment_id' => $comment->id,
            ]);
            $isLiked = true;
            $message = 'Komentar disukai.';

            // Notify comment owner if not self-like
            if ($comment->user_id !== $user->id) {
                Notification::create([
                    'user_id' => $comment->user_id,
                    'type' => 'comment_like',
                    'title' => 'Komentar disukai',
                    'message' => $user->full_name . ' menyukai komentar Anda',
                    'action_url' => route('articles.show', $comment->article_id) . '#comment-' . $comment->id,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_liked' => $isLiked,
            'likes_count' => $comment->likes()->count(),
        ]);
    }

    /**
     * Add or update reaction on a comment.
     */
    public function react(Request $request, Comment $comment): JsonResponse
    {
        $request->validate([
            'reaction' => 'required|string|in:like,love,haha,wow,sad,angry',
        ]);

        $user = Auth::user();
        $reactionType = $request->input('reaction');

        $existingReaction = CommentReaction::where('user_id', $user->id)
            ->where('comment_id', $comment->id)
            ->first();

        if ($existingReaction) {
            if ($existingReaction->reaction === $reactionType) {
                // Same reaction - remove it
                $existingReaction->delete();
                $userReaction = null;
                $message = 'Reaksi dihapus.';
            } else {
                // Different reaction - update it
                $existingReaction->update(['reaction' => $reactionType]);
                $userReaction = $reactionType;
                $message = 'Reaksi diperbarui.';
            }
        } else {
            // New reaction
            CommentReaction::create([
                'user_id' => $user->id,
                'comment_id' => $comment->id,
                'reaction' => $reactionType,
            ]);
            $userReaction = $reactionType;
            $message = 'Reaksi ditambahkan.';

            // Notify comment owner if not self-react
            if ($comment->user_id !== $user->id) {
                $emoji = CommentReaction::REACTIONS[$reactionType] ?? '👍';
                Notification::create([
                    'user_id' => $comment->user_id,
                    'type' => 'comment_reaction',
                    'title' => 'Reaksi baru',
                    'message' => $user->full_name . ' bereaksi ' . $emoji . ' pada komentar Anda',
                    'action_url' => route('articles.show', $comment->article_id) . '#comment-' . $comment->id,
                ]);
            }
        }

        // Get updated reactions summary
        $reactionsSummary = $comment->fresh()->reactions_summary;

        return response()->json([
            'success' => true,
            'message' => $message,
            'user_reaction' => $userReaction,
            'reactions_summary' => $reactionsSummary,
        ]);
    }

    /**
     * Get replies for a comment.
     */
    public function getReplies(Comment $comment): JsonResponse
    {
        $replies = $comment->replies()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($reply) {
                return [
                    'id' => $reply->id,
                    'content' => $reply->content,
                    'created_at' => $reply->created_at->diffForHumans(),
                    'user' => [
                        'id' => $reply->user->id,
                        'full_name' => $reply->user->full_name,
                        'avatar' => $reply->user->avatar
                            ? Storage::url($reply->user->avatar)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($reply->user->full_name) . '&background=14b8a6&color=fff',
                    ],
                    'likes_count' => $reply->likes_count,
                    'is_liked' => $reply->is_liked,
                    'reactions_summary' => $reply->reactions_summary,
                    'user_reaction' => $reply->user_reaction,
                    'replies_count' => $reply->replies()->count(),
                ];
            });

        return response()->json([
            'success' => true,
            'replies' => $replies,
        ]);
    }
}
