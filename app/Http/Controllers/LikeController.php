<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Toggle like on an article.
     */
    public function toggle(Article $article): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Anda harus login terlebih dahulu.'], 401);
        }

        $user = Auth::user();
        $isLiked = $article->isLikedBy($user);

        if ($isLiked) {
            $article->likedBy()->detach($user->id);
            $message = 'Like dihapus.';
            $liked = false;
        } else {
            $article->likedBy()->attach($user->id);
            $message = 'Artikel disukai.';
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'liked' => $liked,
            'likes_count' => $article->likedBy()->count(),
        ]);
    }

    /**
     * Get like count for an article.
     */
    public function count(Article $article): JsonResponse
    {
        return response()->json([
            'count' => $article->likedBy()->count(),
        ]);
    }
}
