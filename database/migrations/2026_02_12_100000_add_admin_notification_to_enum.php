<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'admin_notification' to enum
        // We need to include all existing values PLUS the new one
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('article_approved', 'article_rejected', 'article_revision', 'comment_new', 'comment_reply', 'admin_notification') DEFAULT 'article_approved'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'admin_notification' from enum
        // Warning: This will convert invalid values to empty string or fail depending on SQL mode
        // Ideally we should delete or map them first
        DB::table('notifications')->where('type', 'admin_notification')->delete();

        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('article_approved', 'article_rejected', 'article_revision', 'comment_new', 'comment_reply') DEFAULT 'article_approved'");
    }
};
