<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobApprovedNotification extends Notification implements ShouldQueue
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
            ->subject("Your Job Listing is Live: {$this->job->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Great news! Your job listing **{$this->job->title}** has been reviewed and approved by the JobFinder admin team.")
            ->line("It is now publicly visible to candidates across Sri Lanka.")
            ->action('View Job Listing', route('jobs.show', $this->job))
            ->line('You will be notified whenever candidate applications arrive.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'job_approved',
            'job_id' => $this->job->id,
            'job_title' => $this->job->title,
            'message' => "Your job {$this->job->title} has been approved and is now live!",
            'url' => route('jobs.show', $this->job),
        ];
    }
}
