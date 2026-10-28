<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Notifications\JobExpiredNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BusinessWorkflowAutomationTest extends TestCase
{
    use RefreshDatabase;

    private function createEmployer(): User
    {
        return User::factory()->create([
            'role' => 'employer',
            'status' => 'active',
            'employer_verified_at' => now(),
            'company_name' => 'Nexus Innovations',
        ]);
    }

    public function test_artisan_command_expires_past_deadline_jobs_and_notifies_employer()
    {
        Notification::fake();

        $employer = $this->createEmployer();
        $expiredJob = Job::create([
            'employer_id' => $employer->id,
            'title' => 'Expired Senior Lead',
            'main_category' => 'Technology',
            'location' => 'Colombo',
            'type' => 'Full-time',
            'description' => 'Role description',
            'requirements' => 'Requirements',
            'deadline' => now()->subDay(),
            'status' => 'active',
            'approved_at' => now()->subMonth(),
        ]);

        $futureJob = Job::create([
            'employer_id' => $employer->id,
            'title' => 'Open Active Role',
            'main_category' => 'Technology',
            'location' => 'Colombo',
            'type' => 'Full-time',
            'description' => 'Role description',
            'requirements' => 'Requirements',
            'deadline' => now()->addDays(5),
            'status' => 'active',
            'approved_at' => now()->subDays(2),
        ]);

        Artisan::call('jobs:expire-deadlines');

        $this->assertEquals('expired', $expiredJob->fresh()->status);
        $this->assertEquals('active', $futureJob->fresh()->status);

        Notification::assertSentTo($employer, JobExpiredNotification::class);
    }

    public function test_status_update_creates_audit_history_record()
    {
        $employer = $this->createEmployer();
        $job = Job::create([
            'employer_id' => $employer->id,
            'title' => 'Systems Engineer',
            'main_category' => 'Technology',
            'location' => 'Colombo',
            'type' => 'Full-time',
            'description' => 'Role description',
            'requirements' => 'Requirements',
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $seeker = User::factory()->create(['role' => 'job_seeker']);
        $application = JobApplication::create([
            'job_id' => $job->id,
            'job_seeker_id' => $seeker->id,
            'cover_letter' => 'Test application pitch',
            'resume_path' => 'resumes/cv.pdf',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($employer)->put(route('employer.applications.status', $application), [
            'status' => 'shortlisted',
            'notes' => 'Impressive CV and relevant tech stack match',
        ]);

        $response->assertSessionHas('status');
        $this->assertEquals('shortlisted', $application->fresh()->status);

        $this->assertDatabaseHas('application_status_histories', [
            'job_application_id' => $application->id,
            'changed_by_user_id' => $employer->id,
            'from_status' => 'pending',
            'to_status' => 'shortlisted',
            'notes' => 'Impressive CV and relevant tech stack match',
        ]);
    }

    public function test_dedicated_notification_center_renders_and_deletes()
    {
        $user = User::factory()->create(['role' => 'job_seeker']);

        // Create a dummy notification
        $user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'App\Notifications\JobApprovedNotification',
            'data' => [
                'type' => 'job_approved',
                'message' => 'Your job listing is approved!',
                'url' => route('dashboard'),
            ],
            'read_at' => null,
        ]);

        $response = $this->actingAs($user)->get(route('notifications.index'));
        $response->assertStatus(200);
        $response->assertSee('Notification Center');
        $response->assertSee('Your job listing is approved!');

        $notificationId = $user->notifications()->first()->id;

        // Delete notification
        $delResponse = $this->actingAs($user)->delete(route('notifications.destroy', $notificationId));
        $delResponse->assertSessionHas('status');
        $this->assertDatabaseMissing('notifications', ['id' => $notificationId]);
    }
}
