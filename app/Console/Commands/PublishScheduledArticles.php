<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PublishScheduledArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish articles that have reached their scheduled time';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Use direct DB update to avoid any model event interference
        $count = DB::table('articles')
            ->where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->update([
                'status' => 'published',
                'published_at' => now(),
                'scheduled_at' => null,
                'updated_at' => now(),
            ]);

        if ($count > 0) {
            $this->info("Published {$count} scheduled articles.");
        } else {
            $this->info("No scheduled articles to publish.");
        }

        return Command::SUCCESS;
    }
}
