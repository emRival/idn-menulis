<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class GenerateDailyAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:generate-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily analytics for articles';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Get articles published today
        $articlesPublishedToday = Article::where('status', 'published')
            ->whereDate('published_at', today())
            ->count();

        // Get total articles
        $totalArticles = Article::count();

        // Get total published articles
        $publishedArticles = Article::where('status', 'published')->count();

        // Get articles pending approval
        $pendingArticles = Article::where('status', 'pending')->count();

        $this->info("=== Daily Analytics ===");
        $this->info("Articles published today: {$articlesPublishedToday}");
        $this->info("Total articles: {$totalArticles}");
        $this->info("Published articles: {$publishedArticles}");
        $this->info("Pending approval: {$pendingArticles}");

        return Command::SUCCESS;
    }
}
