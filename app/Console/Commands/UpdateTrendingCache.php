<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class UpdateTrendingCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:update-trending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update trending articles cache';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $trending = Article::where('status', 'published')
            ->orderBy('views_count', 'desc')
            ->limit(5)
            ->get();

        Cache::put('trending_articles', $trending, 30 * 60); // Cache for 30 minutes

        $this->info('Trending articles cache updated.');

        return Command::SUCCESS;
    }
}
