<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleService
{
    /**
     * Create a new article.
     */
    public function createArticle(array $data, User $author): Article
    {
        return DB::transaction(function () use ($data, $author) {
            // Generate unique slug
            $slug = $this->generateSlug($data['title']);

            // Use provided status
            $status = $data['status'] ?? 'draft';

            // Handle featured image
            $featured_image = null;
            if (isset($data['featured_image'])) {
                $featured_image = (new ImageService())->uploadArticleImage($data['featured_image']);
            }

            // Create article
            $article = $author->articles()->create([
                'title' => $data['title'],
                'slug' => $slug,
                'category_id' => $data['category_id'],
                'content' => $data['content'],
                'excerpt' => $data['excerpt'] ?? $this->generateExcerpt($data['content']),
                'featured_image' => $featured_image,
                'status' => $status,
                'scheduled_at' => $data['scheduled_at'] ?? null,
                'published_at' => $data['published_at'] ?? null,
            ]);

            // Calculate reading time
            $article->reading_time = $article->calculateReadingTime();
            $article->save();

            // Attach tags
            if (isset($data['tags']) && is_array($data['tags'])) {
                foreach ($data['tags'] as $tagId) {
                    $tag = Tag::find($tagId);
                    if ($tag) {
                        $article->tags()->attach($tagId);
                        $tag->incrementUsage();
                    }
                }
            }

            return $article;
        });
    }

    /**
     * Update an article.
     */
    public function updateArticle(Article $article, array $data): void
    {
        DB::transaction(function () use ($article, $data) {
            // Handle featured image
            if (isset($data['featured_image'])) {
                if ($article->featured_image) {
                    (new ImageService())->deleteFile($article->featured_image);
                }
                $data['featured_image'] = (new ImageService())->uploadArticleImage($data['featured_image']);
            }

            // Prepare update data
            $updateData = [
                'title' => $data['title'],
                'category_id' => $data['category_id'],
                'content' => $data['content'],
                'excerpt' => $data['excerpt'] ?? $this->generateExcerpt($data['content']),
                'featured_image' => $data['featured_image'] ?? $article->featured_image,
            ];

            // Handle status if provided
            if (isset($data['status'])) {
                $updateData['status'] = $data['status'];
            }

            // Handle scheduling
            if (isset($data['scheduled_at'])) {
                $updateData['scheduled_at'] = $data['scheduled_at'];
            }

            // Handle published_at
            if (isset($data['published_at'])) {
                $updateData['published_at'] = $data['published_at'];
            }

            $article->update($updateData);

            // Recalculate reading time
            $article->reading_time = $article->calculateReadingTime();
            $article->save();

            // Sync tags
            if (isset($data['tags'])) {
                // Get old tags to decrement usage
                $oldTags = $article->tags()->pluck('tags.id')->toArray();
                foreach ($oldTags as $oldTagId) {
                    $tag = Tag::find($oldTagId);
                    if ($tag) {
                        $tag->decrementUsage();
                    }
                }

                // Attach new tags
                $article->tags()->sync($data['tags']);
                foreach ($data['tags'] as $tagId) {
                    $tag = Tag::find($tagId);
                    if ($tag) {
                        $tag->incrementUsage();
                    }
                }
            }
        });
    }

    /**
     * Publish an article immediately.
     */
    public function publishArticle(Article $article): void
    {
        $article->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Schedule an article for publishing.
     */
    public function scheduleArticle(Article $article, string $scheduledAt): void
    {
        $article->update([
            'status' => 'published',
            'scheduled_at' => $scheduledAt,
        ]);
    }

    /**
     * Generate unique slug from title.
     */
    private function generateSlug(string $title): string
    {
        $slug = Str::slug($title);

        // Check if slug exists, if so add random suffix
        if (Article::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . Str::random(6);
        }

        return $slug;
    }

    /**
     * Generate excerpt from content.
     */
    private function generateExcerpt(string $content): string
    {
        $text = strip_tags($content);
        return substr($text, 0, 500) . '...';
    }
}
