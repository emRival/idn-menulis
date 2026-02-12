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
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'is_premium')) {
                $table->boolean('is_premium')->default(false)->after('is_featured');
            }
            if (!Schema::hasColumn('articles', 'is_private')) {
                $table->boolean('is_private')->default(false)->after('is_premium');
            }
            if (!Schema::hasColumn('articles', 'is_encrypted')) {
                $table->boolean('is_encrypted')->default(false)->after('is_private');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $columns = ['is_premium', 'is_private', 'is_encrypted'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('articles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
