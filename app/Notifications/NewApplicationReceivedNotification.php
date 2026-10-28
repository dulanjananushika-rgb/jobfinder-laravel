<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewApplicationReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(public JobApplication $application)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $applicantName = $this->application->jobSeeker->name;
        $jobTitle = $this->application->job->title;

        return (new MailMessage)
            ->subject("New Candidate Applied: {$applicantName} for {$jobTitle}")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have received a new application from **{$applicantName}** for your job listing **{$jobTitle}**.")
            ->action('Review Candidate & Resume', route('employer.applications.index'))
            ->line('You can review their cover letter, download their verified resume, and update their application stage from your Employer Portal.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_application_received',
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
            'job_title' => $this->application->job->title,
            'applicant_name' => $this->application->jobSeeker->name,
            'message' => "New application from {$this->application->jobSeeker->name} for {$this->application->job->title}",
            'url' => route('employer.applications.index'),
        ];
    }
}
