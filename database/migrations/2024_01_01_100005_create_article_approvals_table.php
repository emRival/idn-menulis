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
        Schema::create('article_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('restrict');
            $table->enum('previous_status', ['draft', 'pending', 'published', 'rejected'])->nullable();
            $table->enum('new_status', ['draft', 'pending', 'published', 'rejected'])->index();
            $table->text('notes')->nullable();
            $table->timestamp('reviewed_at')->useCurrent();

            // Index untuk query
            $table->index(['article_id', 'reviewed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_approvals');
    }
};
