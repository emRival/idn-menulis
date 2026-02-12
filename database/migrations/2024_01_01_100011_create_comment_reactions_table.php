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
        Schema::create('comment_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('comment_id')->constrained()->onDelete('cascade');
            $table->string('reaction', 20); // like, love, haha, wow, sad, angry
            $table->timestamp('created_at')->useCurrent();

            // Unique constraint - satu user hanya bisa satu reaksi per komentar
            $table->unique(['user_id', 'comment_id']);

            // Index untuk query performa
            $table->index(['comment_id', 'reaction']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_reactions');
    }
};
