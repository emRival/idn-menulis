<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine if user can view comments.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if user can create comments.
     */
    public function create(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Determine if user can update a comment.
     */
    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    /**
     * Determine if user can delete a comment.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->isGuru() || $user->isAdmin();
    }

    /**
     * Determine if user can approve a comment.
     */
    public function approve(User $user, Comment $comment): bool
    {
        return $user->isGuru() || $user->isAdmin();
    }

    /**
     * Determine if user can reject a comment.
     */
    public function reject(User $user, Comment $comment): bool
    {
        return $user->isGuru() || $user->isAdmin();
    }
}
