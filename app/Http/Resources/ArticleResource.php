<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\EncryptionService;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $encryption = app(EncryptionService::class);

        return [
            'id' => $encryption->encodeId($this->id), // Hashid instead of raw ID
            'slug' => $this->slug,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'content' => $this->when(
                $this->canShowContent($request),
                fn() => $this->getDecryptedContentAttribute(),
                fn() => null
            ),
            'featured_image' => $this->featured_image ? asset('storage/' . $this->featured_image) : null,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'is_premium' => $this->is_premium,
            'is_private' => $this->is_private,
            'reading_time' => $this->reading_time,
            'views_count' => $this->views_count,
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),

            // Relations (only when loaded)
            'author' => $this->whenLoaded('user', fn() => [
                'id' => $encryption->encodeId($this->user->id),
                'username' => $this->user->username,
                'full_name' => $this->user->full_name,
                'avatar' => $this->user->avatar ? asset('storage/' . $this->user->avatar) : null,
            ]),
            'category' => $this->whenLoaded('category', fn() => [
                'id' => $encryption->encodeId($this->category->id),
                'name' => $this->category->name,
                'slug' => $this->category->slug,
                'icon' => $this->category->icon,
            ]),
            'tags' => $this->whenLoaded('tags', fn() => $this->tags->map(fn($tag) => [
                'id' => $encryption->encodeId($tag->id),
                'name' => $tag->name,
                'slug' => $tag->slug,
            ])),

            // Counts
            'likes_count' => $this->whenCounted('likedBy'),
            'comments_count' => $this->whenCounted('comments'),
            'bookmarks_count' => $this->whenCounted('bookmarkedBy'),

            // User interaction (only for authenticated users)
            'is_liked' => $this->when(
                $request->user(),
                fn() => $this->isLikedBy($request->user())
            ),
            'is_bookmarked' => $this->when(
                $request->user(),
                fn() => $this->isBookmarkedBy($request->user())
            ),

            // URLs
            'url' => route('articles.show', $this->slug),
            'edit_url' => $this->when(
                $request->user() && $this->user_id === $request->user()->id,
                fn() => route('articles.edit', $this->slug)
            ),
        ];
    }

    /**
     * Check if content should be shown.
     */
    protected function canShowContent(Request $request): bool
    {
        // Public articles always show content
        if (!$this->is_private && !$this->is_premium) {
            return true;
        }

        $user = $request->user();

        if (!$user) {
            return false;
        }

        return $this->resource->canBeAccessedBy($user);
    }
}
