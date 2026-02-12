<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PublishScheduledArticles
{
    /**
     * Handle an incoming request.
     * Auto-publish scheduled articles when their time has come.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check every 30 seconds to ensure timely publishing
        $cacheKey = 'scheduled_articles_last_check';
        $lastCheck = Cache::get($cacheKey);

        if (!$lastCheck || now()->diffInSeconds($lastCheck) >= 30) {
            try {
                $this->publishScheduledArticles();
            } catch (\Exception $e) {
                Log::error('Failed to publish scheduled articles: ' . $e->getMessage());
            }
            Cache::put($cacheKey, now(), 300); // Cache for 5 minutes max
        }

        return $next($request);
    }

    /**
     * Publish articles that have reached their scheduled time.
     */
    protected function publishScheduledArticles(): int
    {
        return DB::table('articles')
            ->where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->update([
                'status' => 'published',
                'published_at' => now(),
                'scheduled_at' => null,
                'updated_at' => now(),
            ]);
    }
}
