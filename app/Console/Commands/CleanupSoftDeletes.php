<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Console\Command;

class CleanupSoftDeletes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:soft-deletes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete soft-deleted records older than 30 days';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $thirtyDaysAgo = now()->subDays(30);

        // Permanently delete soft-deleted articles
        $articles = Article::onlyTrashed()
            ->where('deleted_at', '<', $thirtyDaysAgo)
            ->forceDelete();

        // Permanently delete soft-deleted comments
        $comments = Comment::onlyTrashed()
            ->where('deleted_at', '<', $thirtyDaysAgo)
            ->forceDelete();

        // Permanently delete soft-deleted users
        $users = User::onlyTrashed()
            ->where('deleted_at', '<', $thirtyDaysAgo)
            ->forceDelete();

        $total = $articles + $comments + $users;

        $this->info("Permanently deleted {$total} soft-deleted records.");

        return Command::SUCCESS;
    }
}
