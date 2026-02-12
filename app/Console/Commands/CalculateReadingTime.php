<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class CalculateReadingTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:calculate-reading-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate reading time for all articles';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $articles = Article::all();

        foreach ($articles as $article) {
            $article->reading_time = $article->calculateReadingTime();
            $article->save();
        }

        $this->info("Updated reading time for {$articles->count()} articles.");

        return Command::SUCCESS;
    }
}
