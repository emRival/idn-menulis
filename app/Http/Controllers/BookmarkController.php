<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    /**
     * Toggle bookmark on an article.
     */
    public function toggle(Article $article): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Anda harus login terlebih dahulu.'], 401);
        }

        $user = Auth::user();
        $isBookmarked = $article->isBookmarkedBy($user);

        if ($isBookmarked) {
            $article->bookmarkedBy()->detach($user->id);
            $message = 'Bookmark dihapus.';
            $bookmarked = false;
        } else {
            $article->bookmarkedBy()->attach($user->id);
            $message = 'Artikel ditambahkan ke bookmark.';
            $bookmarked = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'bookmarked' => $bookmarked,
            'bookmarks_count' => $article->bookmarkedBy()->count(),
        ]);
    }

    /**
     * Get user bookmarks.
     */
    public function myBookmarks()
    {
        $articles = Auth::user()->bookmarks()
            ->where('status', 'published')
            ->orderBy('bookmarks.created_at', 'desc')
            ->paginate(20);

        return view('bookmarks.index', compact('articles'));
    }
}
