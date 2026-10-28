<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationSubmittedNotification extends Notification implements ShouldQueue
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
        $jobTitle = $this->application->job->title;
        $companyName = $this->application->job->employer->company_name ?: $this->application->job->employer->name;

        return (new MailMessage)
            ->subject("Application Confirmation - {$jobTitle} at {$companyName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your application for the position of **{$jobTitle}** at **{$companyName}** has been received successfully.")
            ->action('View Application Status', route('seeker.applications'))
            ->line('The employer will review your profile and update your status accordingly. Good luck with your application!');
    }

    public function toArray(object $notifiable): array
    {
        $job = $this->application->job;
        return [
            'type' => 'application_submitted',
            'application_id' => $this->application->id,
            'job_id' => $job->id,
            'job_title' => $job->title,
            'company_name' => $job->employer->company_name ?: $job->employer->name,
            'message' => "Application submitted for {$job->title}",
            'url' => route('seeker.applications'),
        ];
    }
}
