<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $jobs = Job::active()
            ->with('employer')
            ->when($request->filled('search'), fn ($query) => $query->where('title', 'like', '%'.$request->search.'%'))
            ->when($request->filled('location'), fn ($query) => $query->where('location', 'like', '%'.$request->location.'%'))
            ->when($request->filled('category'), fn ($query) => $query->where('main_category', $request->category))
            ->when($request->filled('type'), fn ($query) => $query->whereIn('type', (array) $request->type))
            ->when($request->filled('experience'), fn ($query) => $query->where('requirements', 'like', '%'.$request->experience.'%'))
            ->when($request->get('sort') === 'oldest', fn ($query) => $query->oldest(), fn ($query) => $query->latest())
            ->paginate(9)
            ->withQueryString();

        return view('jobs.index', [
            'jobs' => $jobs,
            'categories' => Job::active()->select('main_category')->distinct()->pluck('main_category'),
            'locations' => Job::active()->select('location')->distinct()->pluck('location'),
            'types' => Job::active()->select('type')->distinct()->pluck('type'),
        ]);
    }

    public function show(Job $job)
    {
        $job->load('employer');
        abort_unless($job->isOpenForApplications() || $this->canManageJob($job), 404);

        $hasApplied = auth()->check()
            ? auth()->user()->applications()->where('job_id', $job->id)->exists()
            : false;
        $isSaved = auth()->check()
            ? auth()->user()->savedJobs()->where('job_id', $job->id)->exists()
            : false;

        return view('jobs.show', compact('job', 'hasApplied', 'isSaved'));
    }

    public function toggleSave(Job $job)
    {
        $job->load('employer');
        abort_unless($job->isOpenForApplications(), 404);

        $user = auth()->user();
        $user->savedJobs()->toggle($job->id);

        return back()->with('status', 'Saved jobs updated.');
    }

    private function canManageJob(Job $job): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return auth()->user()->isAdmin()
            || (auth()->user()->isEmployer() && auth()->id() === $job->employer_id);
    }
}
