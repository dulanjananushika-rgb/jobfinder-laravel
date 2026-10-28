<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use App\Notifications\ApplicationStatusUpdatedNotification;
use App\Notifications\ApplicationSubmittedNotification;
use App\Notifications\NewApplicationReceivedNotification;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        // Secure Storage: stored in private disk to prevent unauthenticated direct URL access
        $path = $data['resume']->store('resumes', 'local');

        $application = auth()->user()->applications()->create([
            'job_id' => $job->id,
            'cover_letter' => $data['cover_letter'],
            'resume_path' => $path,
            'status' => 'pending',
        ]);

        $application->load('job.employer', 'jobSeeker');

        // Email & In-App Notifications
        if ($application->jobSeeker) {
            $application->jobSeeker->notify(new ApplicationSubmittedNotification($application));
            SmsService::send(
                $application->jobSeeker->phone,
                "JobFinder: Your application for '{$job->title}' has been submitted successfully."
            );
        }

        if ($job->employer) {
            $job->employer->notify(new NewApplicationReceivedNotification($application));
            SmsService::send(
                $job->employer->phone,
                "JobFinder Alert: New application received for '{$job->title}' from {$application->jobSeeker->name}."
            );
        }

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

        $previousStatus = $application->status;
        $application->update($data);

        $application->load('job.employer', 'jobSeeker');

        // Notification on status transition
        if ($application->jobSeeker && $previousStatus !== $data['status']) {
            $application->jobSeeker->notify(new ApplicationStatusUpdatedNotification($application, $previousStatus));
            SmsService::send(
                $application->jobSeeker->phone,
                "JobFinder Alert: Your application for '{$application->job->title}' is now: " . ucfirst($data['status'])
            );
        }

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

    /**
     * Secure Resume Access:
     * Only the applicant themselves, the employer of the job, or an administrator can download the resume.
     */
    public function downloadResume(JobApplication $application)
    {
        $user = auth()->user();
        $application->load('job', 'jobSeeker');

        $isApplicant = $user->id === $application->job_seeker_id;
        $isEmployer = $application->job && $user->id === $application->job->employer_id;
        $isAdmin = $user->isAdmin();

        abort_unless($isApplicant || $isEmployer || $isAdmin, 403, 'Unauthorized access to resume.');

        $disk = Storage::disk('local')->exists($application->resume_path)
            ? 'local'
            : (Storage::disk('public')->exists($application->resume_path) ? 'public' : null);

        abort_unless($disk, 404, 'Resume file not found on server.');

        $extension = pathinfo($application->resume_path, PATHINFO_EXTENSION) ?: 'pdf';
        $safeName = str($application->jobSeeker?->name ?? 'Candidate')->slug() . '-resume.' . $extension;

        return Storage::disk($disk)->download($application->resume_path, $safeName);
    }
}
