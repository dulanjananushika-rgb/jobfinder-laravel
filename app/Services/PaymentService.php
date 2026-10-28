<?php

namespace App\Services;

use App\Models\Job;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Promote a job to Featured status for a given number of days.
     */
    public static function promoteToFeatured(Job $job, int $days = 14, float $amount = 2500.00): bool
    {
        $job->update([
            'is_featured' => true,
            'featured_until' => now()->addDays($days),
        ]);

        Log::info("[Payment Gateway] Job #{$job->id} '{$job->title}' promoted to FEATURED for {$days} days. Amount: LKR {$amount}");

        return true;
    }
}
