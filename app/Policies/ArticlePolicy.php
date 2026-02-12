<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    /**
     * Determine if user can view articles.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if user can view an article.
     */
    public function view(User $user, Article $article): bool
    {
        return $article->isPublished() || $user->id === $article->user_id || $user->isAdmin();
    }

    /**
     * Determine if user can create articles.
     */
    public function create(User $user): bool
    {
        return $user->is_active && ($user->isSiswa() || $user->isGuru() || $user->isAdmin());
    }

    /**
     * Determine if user can update an article.
     */
    public function update(User $user, Article $article): bool
    {
        // Owner can edit their articles in any status, admin can edit any article
        return $user->id === $article->user_id || $user->isAdmin();
    }

    /**
     * Determine if user can delete an article.
     */
    public function delete(User $user, Article $article): bool
    {
        return $user->id === $article->user_id || $user->isAdmin();
    }

    /**
     * Determine if user can publish an article.
     */
    public function publish(User $user, Article $article): bool
    {
        return ($user->id === $article->user_id && $article->status === 'draft') ||
               ($user->isGuru() && $article->status === 'pending') ||
               $user->isAdmin();
    }

    /**
     * Determine if user can approve an article.
     */
    public function approve(User $user, Article $article): bool
    {
        return in_array($article->status, ['pending', 'revision']) && ($user->isGuru() || $user->isAdmin());
    }

    /**
     * Determine if user can review/preview an article for approval.
     */
    public function review(User $user, Article $article): bool
    {
        return $user->isGuru() || $user->isAdmin();
    }
}
