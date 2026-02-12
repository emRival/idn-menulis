<?php

namespace App\Console\Commands;

use App\Models\Tag;
use Illuminate\Console\Command;

class UpdateTagUsageCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tags:update-usage-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update usage count for all tags';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tags = Tag::all();

        foreach ($tags as $tag) {
            $tag->usage_count = $tag->articles()->count();
            $tag->save();
        }

        $this->info("Updated usage count for {$tags->count()} tags.");

        return Command::SUCCESS;
    }
}
