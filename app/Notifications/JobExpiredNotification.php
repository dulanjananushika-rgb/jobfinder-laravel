<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Job $job)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Job Listing Expired: {$this->job->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The application deadline for your job posting **{$this->job->title}** has passed, and the listing has now been marked as expired.")
            ->line('You can review received applications or extend the deadline in your employer console.')
            ->action('Manage Job Vacancies', route('employer.jobs.index'))
            ->line('Thank you for partnering with JobFinder.lk.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'job_expired',
            'job_id' => $this->job->id,
            'job_title' => $this->job->title,
            'message' => "Application deadline passed for '{$this->job->title}'. Listing is now expired.",
            'url' => route('employer.jobs.index'),
        ];
    }
}
