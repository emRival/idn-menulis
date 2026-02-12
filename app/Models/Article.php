<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use App\Services\EncryptionService;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'rejection_reason',
        'is_featured',
        'comments_enabled',
        'views_count',
        'reading_time',
        'published_at',
        'scheduled_at',
        'is_premium',
        'is_private',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'comments_enabled' => 'boolean',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'is_premium' => 'boolean',
        'is_private' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'user_id',
    ];

    /**
     * Get hashid for URL (anti-IDOR).
     */
    public function getHashIdAttribute(): string
    {
        return app(EncryptionService::class)->encodeId($this->id);
    }

    /**
     * Get article by hashid.
     */
    public static function findByHashId(string $hashId): ?self
    {
        $id = app(EncryptionService::class)->decodeId($hashId);
        return $id ? static::find($id) : null;
    }

    /**
     * Get the route key for the model (use slug for public URLs).
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Encrypt content for private/premium articles.
     */
    public function encryptContent(): void
    {
        if (($this->is_private || $this->is_premium) && !$this->isContentEncrypted()) {
            $this->content = Crypt::encryptString($this->content);
            $this->saveQuietly();
        }
    }

    /**
     * Decrypt content for authorized users.
     */
    public function getDecryptedContentAttribute(): string
    {
        if (($this->is_private || $this->is_premium) && $this->isContentEncrypted()) {
            try {
                return Crypt::decryptString($this->content);
            } catch (\Exception $e) {
                return $this->content; // Return as-is if decryption fails
            }
        }
        return $this->content;
    }

    /**
     * Check if content is encrypted.
     */
    protected function isContentEncrypted(): bool
    {
        try {
            Crypt::decryptString($this->content);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Generate signed URL for private article access.
     */
    public function getSignedUrl(int $expiresInMinutes = 60): string
    {
        return URL::temporarySignedRoute(
            'articles.private.show',
            now()->addMinutes($expiresInMinutes),
            ['article' => $this->slug]
        );
    }

    /**
     * Check if user can access this article.
     */
    public function canBeAccessedBy(?User $user): bool
    {
        // Public published articles
        if ($this->isPublished() && !$this->is_private && !$this->is_premium) {
            return true;
        }

        // No user = no access to private/premium
        if (!$user) {
            return false;
        }

        // Author always has access
        if ($this->user_id === $user->id) {
            return true;
        }

        // Admin/Guru have access
        if ($user->isAdmin() || $user->isGuru()) {
            return true;
        }

        // Premium users can access premium content (implement subscription check)
        if ($this->is_premium && $user->hasPremiumAccess()) {
            return true;
        }

        return false;
    }

    /**
     * Get the user who wrote this article.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category this article belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all tags for this article.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tag')
            ->withTimestamps();
    }

    /**
     * Get all comments on this article.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    /**
     * Get all comments including nested replies.
     */
    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get all approvals for this article.
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(ArticleApproval::class)->orderBy('reviewed_at', 'desc');
    }

    /**
     * Get users who liked this article.
     */
    public function likedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes')
            ->withPivot('created_at');
    }

    /**
     * Get users who bookmarked this article.
     */
    public function bookmarkedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bookmarks')
            ->withPivot('created_at');
    }

    /**
     * Check if article is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at <= now();
    }

    /**
     * Check if article is pending review.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if article is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if article needs revision.
     */
    public function needsRevision(): bool
    {
        return $this->status === 'revision';
    }

    /**
     * Check if article is scheduled.
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    /**
     * Check if user has liked this article.
     */
    public function isLikedBy(User $user): bool
    {
        return $this->likedBy->contains($user->id);
    }

    /**
     * Check if user has bookmarked this article.
     */
    public function isBookmarkedBy(User $user): bool
    {
        return $this->bookmarkedBy->contains($user->id);
    }

    /**
     * Increment view count.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Calculate reading time (estimate 200 words per minute).
     */
    public function calculateReadingTime(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, (int) ceil($wordCount / 200));
    }

    /**
     * Scope to get published articles only.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to get scheduled articles.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled')
            ->whereNotNull('scheduled_at');
    }

    /**
     * Scope to get articles by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to get articles by author.
     */
    public function scopeByAuthor($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get pending articles.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
