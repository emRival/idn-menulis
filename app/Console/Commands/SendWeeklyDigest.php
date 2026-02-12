<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\User;
use Illuminate\Console\Command;

class SendWeeklyDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:weekly-digest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly digest email to active users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Get active users
        $users = User::where('is_active', true)->get();

        // Get top articles from last week
        $articles = Article::where('status', 'published')
            ->where('published_at', '>=', now()->subWeek())
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();

        $this->info("Weekly digest would be sent to {$users->count()} users with {$articles->count()} top articles.");
        $this->info("Note: Email sending not implemented yet. Configure mail service in .env");

        return Command::SUCCESS;
    }
}
