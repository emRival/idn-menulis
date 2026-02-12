<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add deleted_by_admin to comments table
        Schema::table('comments', function (Blueprint $table) {
            $table->boolean('deleted_by_admin')->default(false)->after('is_approved');
            $table->foreignId('deleted_by')->nullable()->after('deleted_by_admin')->constrained('users')->nullOnDelete();
        });

        // Add comments_enabled to articles table
        Schema::table('articles', function (Blueprint $table) {
            $table->boolean('comments_enabled')->default(true)->after('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropColumn(['deleted_by_admin', 'deleted_by']);
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('comments_enabled');
        });
    }
};
