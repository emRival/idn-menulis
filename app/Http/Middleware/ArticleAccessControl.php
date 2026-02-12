<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticleAccessControl
{
    /**
     * Check if user can access private/premium articles.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $article = $request->route('article');

        if (!$article) {
            return $next($request);
        }

        // If article model is not loaded, try to load it
        if (is_string($article)) {
            $article = \App\Models\Article::where('slug', $article)->first();

            if (!$article) {
                abort(404);
            }
        }

        // Check access for private/premium articles
        if ($article->is_private || $article->is_premium) {
            $user = $request->user();

            if (!$article->canBeAccessedBy($user)) {
                // Check for signed URL
                if ($request->hasValidSignature()) {
                    return $next($request);
                }

                if ($article->is_premium) {
                    abort(403, 'Konten ini hanya tersedia untuk pengguna premium.');
                }

                abort(403, 'Anda tidak memiliki akses ke konten ini.');
            }
        }

        return $next($request);
    }
}
