<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Interview;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return view('admin.dashboard', [
                'usersCount' => User::count(),
                'seekersCount' => User::where('role', 'job_seeker')->count(),
                'employersCount' => User::where('role', 'employer')->count(),
                'jobsCount' => Job::count(),
                'activeJobsCount' => Job::where('status', 'active')->count(),
                'pendingJobsCount' => Job::where('status', 'pending')->count(),
                'applicationsCount' => JobApplication::count(),
                'messagesCount' => ContactMessage::where('status', 'new')->count(),
                'recentUsers' => User::latest()->limit(5)->get(),
                'recentJobs' => Job::with('employer')->latest()->limit(5)->get(),
            ]);
        }

        if ($user->isEmployer()) {
            $jobIds = $user->jobs()->pluck('id');

            $statusBreakdown = [
                'pending' => JobApplication::whereIn('job_id', $jobIds)->where('status', 'pending')->count(),
                'reviewed' => JobApplication::whereIn('job_id', $jobIds)->where('status', 'reviewed')->count(),
                'shortlisted' => JobApplication::whereIn('job_id', $jobIds)->where('status', 'shortlisted')->count(),
                'interview' => JobApplication::whereIn('job_id', $jobIds)->where('status', 'interview')->count(),
                'hired' => JobApplication::whereIn('job_id', $jobIds)->where('status', 'hired')->count(),
                'rejected' => JobApplication::whereIn('job_id', $jobIds)->where('status', 'rejected')->count(),
            ];

            return view('employer.dashboard', [
                'jobs' => $user->jobs()->withCount('applications')->latest()->limit(6)->get(),
                'activeJobs' => $user->jobs()->active()->count(),
                'totalViews' => $user->jobs()->sum('views_count'),
                'applicationsCount' => JobApplication::whereIn('job_id', $jobIds)->count(),
                'statusBreakdown' => $statusBreakdown,
                'upcomingInterviews' => Interview::where('employer_id', $user->id)
                    ->where('scheduled_at', '>=', now())
                    ->with('candidate', 'application.job')
                    ->orderBy('scheduled_at')
                    ->limit(5)
                    ->get(),
            ]);
        }

        // Job Seeker
        return view('seeker.dashboard', [
            'applications' => $user->applications()->with('job.employer', 'interview')->latest()->limit(5)->get(),
            'savedJobs' => $user->savedJobs()->with('employer')->latest()->limit(5)->get(),
            'recommendedJobs' => Job::active()->with('employer')->latest()->limit(6)->get(),
            'upcomingInterviews' => Interview::where('candidate_id', $user->id)
                ->where('scheduled_at', '>=', now())
                ->with('employer', 'application.job')
                ->orderBy('scheduled_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
