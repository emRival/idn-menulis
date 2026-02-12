<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixArticleImagePaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:fix-image-paths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix relative image paths in article content to absolute paths';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fix ../storage/ to /storage/
        $count1 = DB::table('articles')
            ->where('content', 'like', '%../storage/%')
            ->update([
                'content' => DB::raw("REPLACE(content, '../storage/', '/storage/')")
            ]);

        // Fix ./storage/ to /storage/
        $count2 = DB::table('articles')
            ->where('content', 'like', '%./storage/%')
            ->update([
                'content' => DB::raw("REPLACE(content, './storage/', '/storage/')")
            ]);

        $this->info("Fixed {$count1} articles with '../storage/' paths.");
        $this->info("Fixed {$count2} articles with './storage/' paths.");

        return Command::SUCCESS;
    }
}
