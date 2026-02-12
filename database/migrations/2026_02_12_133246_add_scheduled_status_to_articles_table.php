<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'scheduled' to status ENUM for articles table
        DB::statement("ALTER TABLE articles MODIFY COLUMN status ENUM('draft', 'pending', 'revision', 'scheduled', 'published', 'rejected') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First update any 'scheduled' articles to 'draft'
        DB::table('articles')->where('status', 'scheduled')->update(['status' => 'draft']);

        // Remove 'scheduled' from status ENUM
        DB::statement("ALTER TABLE articles MODIFY COLUMN status ENUM('draft', 'pending', 'revision', 'published', 'rejected') DEFAULT 'draft'");
    }
};
