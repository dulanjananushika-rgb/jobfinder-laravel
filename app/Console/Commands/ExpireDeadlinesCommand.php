<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Notifications\JobExpiredNotification;
use Illuminate\Console\Command;

class ExpireDeadlinesCommand extends Command
{
    protected $signature = 'jobs:expire-deadlines';
    protected $description = 'Finds active jobs past their deadline and transitions them to expired status';

    public function handle(): int
    {
        $expiredJobs = Job::where('status', 'active')
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', today())
            ->with('employer')
            ->get();

        $count = $expiredJobs->count();

        foreach ($expiredJobs as $job) {
            $job->update(['status' => 'expired']);

            if ($job->employer) {
                $job->employer->notify(new JobExpiredNotification($job));
            }
        }

        $this->info("Successfully transitioned {$count} expired job listings.");
        return Command::SUCCESS;
    }
}
