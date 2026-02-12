<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'revision' to new_status ENUM
        DB::statement("ALTER TABLE article_approvals MODIFY COLUMN new_status ENUM('draft', 'pending', 'revision', 'published', 'rejected')");

        // Add 'revision' to previous_status ENUM
        DB::statement("ALTER TABLE article_approvals MODIFY COLUMN previous_status ENUM('draft', 'pending', 'revision', 'published', 'rejected')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE article_approvals MODIFY COLUMN new_status ENUM('draft', 'pending', 'published', 'rejected')");
        DB::statement("ALTER TABLE article_approvals MODIFY COLUMN previous_status ENUM('draft', 'pending', 'published', 'rejected')");
    }
};
