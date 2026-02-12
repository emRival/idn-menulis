<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_id',
        'user_id',
        'parent_id',
        'content',
        'is_approved',
        'deleted_by_admin',
        'deleted_by',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['likes_count', 'is_liked', 'reactions_summary', 'user_reaction'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_approved' => 'boolean',
        'deleted_by_admin' => 'boolean',
    ];

    /**
     * Get the article this comment belongs to.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the user who made this comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who deleted this comment.
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Check if this comment was deleted by admin.
     */
    public function isDeletedByAdmin(): bool
    {
        return $this->deleted_by_admin === true;
    }

    /**
     * Get the parent comment if this is a reply.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get all replies to this comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->where('is_approved', true);
    }

    /**
     * Get all replies including unapproved.
     */
    public function allReplies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Check if comment is a reply.
     */
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Scope to get approved comments.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope to get unapproved comments.
     */
    public function scopeUnapproved($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope to get top-level comments only.
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get all likes for this comment.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(CommentLike::class);
    }

    /**
     * Get all reactions for this comment.
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(CommentReaction::class);
    }

    /**
     * Get likes count attribute.
     */
    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    /**
     * Check if current user liked this comment.
     */
    public function getIsLikedAttribute(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        return $this->likes()->where('user_id', Auth::id())->exists();
    }

    /**
     * Get reactions summary (grouped by type with count).
     */
    public function getReactionsSummaryAttribute(): array
    {
        $reactions = $this->reactions()
            ->selectRaw('reaction, count(*) as count')
            ->groupBy('reaction')
            ->pluck('count', 'reaction')
            ->toArray();

        $summary = [];
        foreach ($reactions as $type => $count) {
            $summary[] = [
                'type' => $type,
                'emoji' => CommentReaction::REACTIONS[$type] ?? '👍',
                'count' => $count,
            ];
        }
        return $summary;
    }

    /**
     * Get current user's reaction on this comment.
     */
    public function getUserReactionAttribute(): ?string
    {
        if (!Auth::check()) {
            return null;
        }
        $reaction = $this->reactions()->where('user_id', Auth::id())->first();
        return $reaction ? $reaction->reaction : null;
    }

    /**
     * Check if user has liked this comment.
     */
    public function isLikedBy($userId): bool
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Check if user has reacted to this comment.
     */
    public function hasReactionFrom($userId): bool
    {
        return $this->reactions()->where('user_id', $userId)->exists();
    }

    /**
     * Get user's reaction type.
     */
    public function getReactionFrom($userId): ?string
    {
        $reaction = $this->reactions()->where('user_id', $userId)->first();
        return $reaction ? $reaction->reaction : null;
    }
}
