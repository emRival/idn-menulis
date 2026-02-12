<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;

class SendPendingApprovalsReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:pending-approvals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to guru about pending article approvals';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $pendingCount = Article::where('status', 'pending')->count();

        if ($pendingCount === 0) {
            $this->info('No pending articles.');
            return Command::SUCCESS;
        }

        // Get all guru users
        $gurus = User::where('role', 'guru')->get();

        foreach ($gurus as $guru) {
            Notification::create([
                'user_id' => $guru->id,
                'type' => 'pending_approvals',
                'title' => 'Pengingat: Artikel Menunggu Persetujuan',
                'message' => "Ada {$pendingCount} artikel yang menunggu persetujuan Anda.",
                'action_url' => route('approvals.pending'),
            ]);
        }

        $this->info("Sent pending approval reminders to {$gurus->count()} guru.");

        return Command::SUCCESS;
    }
}
