<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Services\SmsService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function show(JobApplication $application)
    {
        $user = auth()->user();
        $application->load('job.employer', 'jobSeeker', 'messages.sender', 'interview');

        $isApplicant = $user->id === $application->job_seeker_id;
        $isEmployer = $application->job && $user->id === $application->job->employer_id;
        $isAdmin = $user->isAdmin();

        abort_unless($isApplicant || $isEmployer || $isAdmin, 403, 'Unauthorized access to candidate message thread.');

        // Mark unread messages as read
        $application->messages()->where('sender_id', '!=', $user->id)->whereNull('read_at')->update(['read_at' => now()]);

        return view('applications.messages', compact('application'));
    }

    public function store(Request $request, JobApplication $application)
    {
        $user = auth()->user();
        $application->load('job.employer', 'jobSeeker');

        $isApplicant = $user->id === $application->job_seeker_id;
        $isEmployer = $application->job && $user->id === $application->job->employer_id;
        $isAdmin = $user->isAdmin();

        abort_unless($isApplicant || $isEmployer || $isAdmin, 403, 'Unauthorized access to send message.');

        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = $application->messages()->create([
            'sender_id' => $user->id,
            'message' => $data['message'],
        ]);

        // Send notification & SMS to recipient
        $recipient = $isApplicant ? $application->job->employer : $application->jobSeeker;
        if ($recipient) {
            SmsService::send(
                $recipient->phone,
                "JobFinder Alert: New message from {$user->name} regarding application #APP-{$application->id}."
            );
        }

        return back()->with('status', 'Message sent successfully.');
    }
}
