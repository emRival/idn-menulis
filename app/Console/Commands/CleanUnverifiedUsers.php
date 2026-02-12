<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CleanUnverifiedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:clean-unverified';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete unverified users older than 7 days';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $sevenDaysAgo = now()->subDays(7);

        $users = User::whereNull('email_verified_at')
            ->where('created_at', '<', $sevenDaysAgo)
            ->delete();

        $this->info("Deleted {$users} unverified users.");

        return Command::SUCCESS;
    }
}
