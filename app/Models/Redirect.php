<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Redirect extends Model
{
    protected $fillable = [
        'from_url',
        'to_url',
        'status_code',
        'hits',
        'last_hit_at',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'hits' => 'integer',
        'last_hit_at' => 'datetime',
    ];

    /**
     * Get active redirect for a URL
     */
    public static function findRedirect(string $url): ?self
    {
        return Cache::remember("redirect:{$url}", now()->addHour(), function () use ($url) {
            return static::where('from_url', $url)
                ->where('is_active', true)
                ->first();
        });
    }

    /**
     * Record a hit on the redirect
     */
    public function recordHit(): void
    {
        $this->increment('hits');
        $this->update(['last_hit_at' => now()]);
    }

    /**
     * Clear redirect cache
     */
    public static function clearCache(?string $url = null): void
    {
        if ($url) {
            Cache::forget("redirect:{$url}");
        }

        // Also clear pattern cache
        Cache::forget('redirects_all');
    }

    /**
     * Get all active redirects (for bulk operations)
     */
    public static function getAllActive()
    {
        return Cache::remember('redirects_all', now()->addHour(), function () {
            return static::where('is_active', true)->get();
        });
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($redirect) {
            static::clearCache($redirect->from_url);
        });

        static::deleted(function ($redirect) {
            static::clearCache($redirect->from_url);
        });
    }
}
