<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\JobApplication;
use App\Notifications\ApplicationStatusUpdatedNotification;
use App\Services\SmsService;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function store(Request $request, JobApplication $application)
    {
        $user = auth()->user();
        $application->load('job.employer', 'jobSeeker');

        abort_unless($application->job && $user->id === $application->job->employer_id, 403);
        abort_if($application->status === 'withdrawn', 422, 'Cannot schedule interview on a withdrawn application.');

        $data = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'meeting_link' => ['nullable', 'url', 'max:500'],
            'location_type' => ['required', 'in:remote,in_person'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $interview = Interview::updateOrCreate(
            ['job_application_id' => $application->id],
            [
                'employer_id' => $user->id,
                'candidate_id' => $application->job_seeker_id,
                'scheduled_at' => $data['scheduled_at'],
                'meeting_link' => $data['meeting_link'] ?? null,
                'location_type' => $data['location_type'],
                'notes' => $data['notes'] ?? null,
                'status' => 'scheduled',
            ]
        );

        // Update application status to interview
        $previousStatus = $application->status;
        $application->update(['status' => 'interview']);

        // Notify candidate
        if ($application->jobSeeker) {
            $application->jobSeeker->notify(new ApplicationStatusUpdatedNotification($application, $previousStatus));
            SmsService::send(
                $application->jobSeeker->phone,
                "JobFinder Alert: Interview invitation received from {$user->company_name} for {$application->job->title} scheduled for {$interview->scheduled_at->format('M d, Y h:i A')}."
            );
        }

        return back()->with('status', 'Interview successfully scheduled and invitation dispatched to candidate.');
    }
}
