<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use App\Notifications\ApplicationStatusUpdatedNotification;
use App\Notifications\ApplicationSubmittedNotification;
use App\Notifications\NewApplicationReceivedNotification;
use App\Services\AiCandidateMatchingService;
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

        return view('applications.create', [
            'job' => $job,
            'user' => auth()->user(),
        ]);
    }

    public function store(Request $request, Job $job)
    {
        $user = auth()->user();
        $job->load('employer');
        abort_unless($job->isOpenForApplications(), 404);
        abort_if($user->applications()->where('job_id', $job->id)->exists(), 409);

        $applyType = $request->input('apply_type', 'upload');

        $data = $request->validate([
            'cover_letter' => ['required', 'string', 'max:2000'],
            'resume' => [$applyType === 'profile' ? 'nullable' : 'required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'screening_answers' => ['nullable', 'array'],
            'screening_answers.*' => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->hasFile('resume')) {
            $path = $data['resume']->store('resumes', 'local');
        } else {
            // Generate snapshot CV from Seeker Profile
            $path = 'resumes/profile_' . $user->id . '_' . time() . '.txt';
            Storage::disk('local')->put(
                $path,
                "JOBFINDER VERIFIED PROFILE CV\n" .
                "============================\n" .
                "Name: {$user->name}\n" .
                "Email: {$user->email}\n" .
                "Phone: {$user->phone}\n" .
                "Headline: {$user->headline}\n" .
                "Key Skills: {$user->skills}\n\n" .
                "PROFESSIONAL EXPERIENCE:\n{$user->experience}\n\n" .
                "EDUCATION & QUALIFICATIONS:\n{$user->education}\n\n" .
                "ONLINE PROFILES:\n" .
                "Portfolio: {$user->portfolio_url}\n" .
                "GitHub: {$user->github_url}\n" .
                "LinkedIn: {$user->linkedin_url}\n"
            );
        }

        // Run AI ATS Match Evaluation
        $aiResult = AiCandidateMatchingService::evaluate($job, $user, $data['cover_letter']);

        $application = $user->applications()->create([
            'job_id' => $job->id,
            'cover_letter' => $data['cover_letter'],
            'screening_answers' => $data['screening_answers'] ?? null,
            'resume_path' => $path,
            'status' => 'pending',
            'ai_match_score' => $aiResult['score'],
            'ai_summary' => $aiResult['summary'],
            'apply_type' => $applyType,
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
                "JobFinder Alert: New application received for '{$job->title}' from {$application->jobSeeker->name} (Match: {$aiResult['score']}%)."
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
            'applications' => auth()->user()->applications()->with('job.employer', 'interview')->latest()->paginate(10),
        ]);
    }

    public function employerIndex(Request $request)
    {
        $applications = JobApplication::with('job', 'jobSeeker', 'interview', 'messages')
            ->whereHas('job', fn ($query) => $query->where('employer_id', auth()->id()))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('min_match'), fn ($query) => $query->where('ai_match_score', '>=', (int) $request->min_match))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('employer.applications', compact('applications'));
    }

    public function exportCsv(Request $request)
    {
        abort_unless(auth()->user()->isEmployer(), 403);

        $applications = JobApplication::with('job', 'jobSeeker')
            ->whereHas('job', fn ($query) => $query->where('employer_id', auth()->id()))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('job_id'), fn ($query) => $query->where('job_id', $request->job_id))
            ->latest()
            ->get();

        $filename = 'candidates-export-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($applications) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Application ID',
                'Job Title',
                'Candidate Name',
                'Candidate Email',
                'Candidate Phone',
                'AI Match Score',
                'Status',
                'Applied Date',
                'Screening Q&A',
                'Cover Letter Pitch',
            ]);

            foreach ($applications as $app) {
                $screeningFormatted = '';
                if ($app->job && ! empty($app->job->screening_questions) && ! empty($app->screening_answers)) {
                    $qaPairs = [];
                    foreach ($app->job->screening_questions as $idx => $q) {
                        $ans = $app->screening_answers[$idx] ?? 'No answer';
                        $qaPairs[] = "Q: {$q} | A: {$ans}";
                    }
                    $screeningFormatted = implode(' || ', $qaPairs);
                }

                fputcsv($handle, [
                    '#APP-' . $app->id,
                    $app->job?->title ?? 'N/A',
                    $app->jobSeeker?->name ?? 'N/A',
                    $app->jobSeeker?->email ?? 'N/A',
                    $app->jobSeeker?->phone ?? 'N/A',
                    $app->ai_match_score ? $app->ai_match_score . '%' : 'N/A',
                    ucfirst($app->status),
                    $app->created_at->format('Y-m-d H:i'),
                    $screeningFormatted,
                    $app->cover_letter,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function updateStatus(Request $request, JobApplication $application)
    {
        abort_unless($application->job()->where('employer_id', auth()->id())->exists(), 403);
        abort_if($application->status === 'withdrawn', 422, 'Withdrawn applications cannot be updated.');

        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'reviewed', 'shortlisted', 'interview', 'hired', 'rejected'])],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $previousStatus = $application->status;
        $application->update(['status' => $data['status']]);

        // Record status change in audit log
        $application->statusHistories()->create([
            'changed_by_user_id' => auth()->id(),
            'from_status' => $previousStatus,
            'to_status' => $data['status'],
            'notes' => $data['notes'] ?? null,
        ]);

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

        $extension = pathinfo($application->resume_path, PATHINFO_EXTENSION) ?: 'txt';
        $safeName = str($application->jobSeeker?->name ?? 'Candidate')->slug() . '-resume.' . $extension;

        return Storage::disk($disk)->download($application->resume_path, $safeName);
    }
}
