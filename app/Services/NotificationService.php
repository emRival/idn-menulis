<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Send notification to user.
     */
    public function send(User $user, string $type, string $title, string $message, ?string $actionUrl = null): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
        ]);
    }

    /**
     * Send article approved notification.
     */
    public function sendArticleApproved(User $user, string $articleTitle, string $actionUrl): void
    {
        $this->send(
            $user,
            'article_approved',
            'Artikel Disetujui',
            'Artikel "' . $articleTitle . '" telah disetujui dan dipublikasikan.',
            $actionUrl
        );
    }

    /**
     * Send article rejected notification.
     */
    public function sendArticleRejected(User $user, string $articleTitle, string $actionUrl): void
    {
        $this->send(
            $user,
            'article_rejected',
            'Artikel Ditolak',
            'Artikel "' . $articleTitle . '" telah ditolak. Silakan baca keterangan penolakan dan revisi artikel Anda.',
            $actionUrl
        );
    }

    /**
     * Send comment notification.
     */
    public function sendCommentNotification(User $user, string $authorName, string $articleTitle, string $actionUrl): void
    {
        $this->send(
            $user,
            'comment_new',
            'Komentar Baru',
            $authorName . ' mengomentari artikel "' . $articleTitle . '"',
            $actionUrl
        );
    }

    /**
     * Send comment reply notification.
     */
    public function sendCommentReplyNotification(User $user, string $authorName, string $actionUrl): void
    {
        $this->send(
            $user,
            'comment_reply',
            'Balasan Komentar',
            $authorName . ' membalas komentar Anda',
            $actionUrl
        );
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification): void
    {
        $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for user.
     */
    public function markAllAsRead(User $user): void
    {
        $user->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}
