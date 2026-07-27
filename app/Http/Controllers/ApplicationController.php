<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    public function create(Job $job)
    {
        $job->load('employer');
        abort_unless($job->isOpenForApplications(), 404);
        abort_if(auth()->user()->applications()->where('job_id', $job->id)->exists(), 409);

        return view('applications.create', compact('job'));
    }

    public function store(Request $request, Job $job)
    {
        $job->load('employer');
        abort_unless($job->isOpenForApplications(), 404);
        abort_if(auth()->user()->applications()->where('job_id', $job->id)->exists(), 409);

        $data = $request->validate([
            'cover_letter' => ['required', 'string', 'max:2000'],
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $path = $data['resume']->store('resumes', 'public');

        $application = auth()->user()->applications()->create([
            'job_id' => $job->id,
            'cover_letter' => $data['cover_letter'],
            'resume_path' => $path,
            'status' => 'pending',
        ]);

        return redirect()->route('applications.confirmation', $application);
    }

    public function confirmation(JobApplication $application)
    {
        abort_unless($application->job_seeker_id === auth()->id(), 403);

        return view('applications.confirmation', [
            'application' => $application->load('job.employer'),
        ]);
    }

    public function seekerIndex()
    {
        return view('seeker.applications', [
            'applications' => auth()->user()->applications()->with('job.employer')->latest()->paginate(10),
        ]);
    }

    public function employerIndex(Request $request)
    {
        $applications = JobApplication::with('job', 'jobSeeker')
            ->whereHas('job', fn ($query) => $query->where('employer_id', auth()->id()))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('employer.applications', compact('applications'));
    }

    public function updateStatus(Request $request, JobApplication $application)
    {
        abort_unless($application->job()->where('employer_id', auth()->id())->exists(), 403);
        abort_if($application->status === 'withdrawn', 422, 'Withdrawn applications cannot be updated.');

        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'reviewed', 'shortlisted', 'interview', 'hired', 'rejected'])],
        ]);

        $application->update($data);

        return back()->with('status', 'Application status updated.');
    }

    public function withdraw(Request $request, JobApplication $application)
    {
        abort_unless($application->job_seeker_id === auth()->id(), 403);
        abort_if($application->status === 'withdrawn', 422, 'Application is already withdrawn.');
        abort_if(in_array($application->status, ['hired', 'rejected'], true), 422, 'Finalized applications cannot be withdrawn.');

        $data = $request->validate([
            'withdraw_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $application->update([
            'status' => 'withdrawn',
            'withdrawn_at' => now(),
            'withdraw_reason' => $data['withdraw_reason'] ?? null,
        ]);

        return back()->with('status', 'Application withdrawn.');
    }
}
