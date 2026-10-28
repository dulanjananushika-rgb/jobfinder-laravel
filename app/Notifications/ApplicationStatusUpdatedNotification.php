<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(public JobApplication $application, public string $previousStatus = 'pending')
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $jobTitle = $this->application->job->title;
        $companyName = $this->application->job->employer->company_name ?: $this->application->job->employer->name;
        $status = ucfirst($this->application->status);

        $statusText = match ($this->application->status) {
            'shortlisted' => 'Congratulations! You have been shortlisted for this role.',
            'interview' => 'Great news! The employer would like to invite you for an interview. Please keep your phone and email active.',
            'hired' => 'Congratulations! You have been marked as HIRED for this position! 🎉',
            'rejected' => 'Thank you for your interest. The employer has decided to move forward with other candidates at this time.',
            default => "Your application status has been updated to: {$status}."
        };

        return (new MailMessage)
            ->subject("Application Update: {$status} - {$jobTitle} at {$companyName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The status of your application for **{$jobTitle}** at **{$companyName}** has been updated to: **{$status}**.")
            ->line($statusText)
            ->action('View Application Details', route('seeker.applications'))
            ->line('Thank you for using JobFinder.lk.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'status_updated',
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
            'job_title' => $this->application->job->title,
            'status' => $this->application->status,
            'message' => "Your application for {$this->application->job->title} is now: " . ucfirst($this->application->status),
            'url' => route('seeker.applications'),
        ];
    }
}
