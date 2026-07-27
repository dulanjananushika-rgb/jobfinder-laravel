<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class EmployerJobController extends Controller
{
    public function index()
    {
        return view('employer.jobs.index', [
            'jobs' => auth()->user()->jobs()->withCount('applications')->latest()->paginate(10),
        ]);
    }

    public function create()
    {
        abort_unless(auth()->user()->isVerifiedEmployer(), 403, 'Your employer account must be verified before posting jobs.');
        return view('employer.jobs.form', ['job' => new Job()]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->isVerifiedEmployer(), 403, 'Your employer account must be verified before posting jobs.');

        auth()->user()->jobs()->create([
            ...$this->validated($request),
            'status' => 'pending',
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
        ]);

        return redirect()->route('employer.jobs.index')->with('status', 'Job submitted for admin approval.');
    }

    public function edit(Job $job)
    {
        $this->authorizeEmployer($job);
        return view('employer.jobs.form', compact('job'));
    }

    public function update(Request $request, Job $job)
    {
        $this->authorizeEmployer($job);
        $job->update([
            ...$this->validated($request),
            'status' => 'pending',
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
        ]);

        return redirect()->route('employer.jobs.index')->with('status', 'Job updated and sent for re-approval.');
    }

    public function destroy(Job $job)
    {
        $this->authorizeEmployer($job);
        $job->delete();

        return back()->with('status', 'Job deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'main_category' => ['required', 'string', 'max:100'],
            'sub_category' => ['nullable', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'max:100'],
            'salary' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:20'],
            'requirements' => ['required', 'string', 'min:10'],
            'responsibilities' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
        ]);
    }

    private function authorizeEmployer(Job $job): void
    {
        abort_unless($job->employer_id === auth()->id(), 403);
    }
}
