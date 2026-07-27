<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\ContactMessage;
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
                'jobsCount' => Job::count(),
                'applicationsCount' => JobApplication::count(),
                'messagesCount' => ContactMessage::where('status', 'new')->count(),
                'recentUsers' => User::latest()->limit(5)->get(),
                'recentJobs' => Job::with('employer')->latest()->limit(5)->get(),
            ]);
        }

        if ($user->isEmployer()) {
            return view('employer.dashboard', [
                'jobs' => $user->jobs()->withCount('applications')->latest()->limit(6)->get(),
                'activeJobs' => $user->jobs()->active()->count(),
                'applicationsCount' => JobApplication::whereHas('job', fn ($query) => $query->where('employer_id', $user->id))->count(),
            ]);
        }

        return view('seeker.dashboard', [
            'applications' => $user->applications()->with('job.employer')->latest()->limit(5)->get(),
            'savedJobs' => $user->savedJobs()->with('employer')->latest()->limit(5)->get(),
            'recommendedJobs' => Job::active()->with('employer')->latest()->limit(6)->get(),
        ]);
    }
}
