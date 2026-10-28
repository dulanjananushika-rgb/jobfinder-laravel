<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class FeaturedJobController extends Controller
{
    public function promote(Request $request, Job $job)
    {
        $user = auth()->user();
        abort_unless($user->isEmployer() && $job->employer_id === $user->id, 403);
        abort_unless($job->status === 'active', 422, 'Only active approved jobs can be promoted to Featured.');

        PaymentService::promoteToFeatured($job, 14, 2500.00);

        return back()->with('status', "Job '{$job->title}' has been successfully promoted to Featured for 14 days!");
    }
}
