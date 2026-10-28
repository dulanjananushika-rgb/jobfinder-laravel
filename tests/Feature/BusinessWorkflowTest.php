<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_jobs_only_show_approved_jobs_from_verified_active_employers(): void
    {
        $verifiedEmployer = User::factory()->create([
            'role' => 'employer',
            'status' => 'active',
            'employer_verified_at' => now(),
        ]);
        $unverifiedEmployer = User::factory()->create([
            'role' => 'employer',
            'status' => 'active',
            'employer_verified_at' => null,
        ]);

        $visibleJob = $this->jobFor($verifiedEmployer, ['title' => 'Visible approved job', 'approved_at' => now()]);
        $this->jobFor($verifiedEmployer, ['title' => 'Pending hidden job', 'status' => 'pending', 'approved_at' => null]);
        $this->jobFor($unverifiedEmployer, ['title' => 'Unverified hidden job', 'approved_at' => now()]);
        $inactiveEmployer = User::factory()->create([
            'role' => 'employer',
            'status' => 'inactive',
            'employer_verified_at' => now(),
        ]);
        $inactiveEmployerJob = $this->jobFor($inactiveEmployer, ['title' => 'Inactive employer hidden job', 'approved_at' => now()]);

        $this->get(route('jobs.index'))
            ->assertOk()
            ->assertSee($visibleJob->title)
            ->assertDontSee('Pending hidden job')
            ->assertDontSee('Unverified hidden job')
            ->assertDontSee('Inactive employer hidden job');

        $this->get(route('jobs.show', $inactiveEmployerJob))->assertNotFound();
    }

    public function test_unverified_employer_cannot_post_and_admin_cannot_approve_their_jobs(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $employer = User::factory()->create([
            'role' => 'employer',
            'status' => 'active',
            'employer_verified_at' => null,
        ]);
        $job = $this->jobFor($employer, ['status' => 'pending', 'approved_at' => null]);

        $this->actingAs($employer)->get(route('employer.jobs.create'))->assertForbidden();

        $this->actingAs($admin)
            ->put(route('admin.jobs.update', $job), ['status' => 'active'])
            ->assertUnprocessable();
    }

    public function test_expired_jobs_cannot_be_approved(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $employer = User::factory()->create([
            'role' => 'employer',
            'status' => 'active',
            'employer_verified_at' => now(),
        ]);
        $job = $this->jobFor($employer, [
            'status' => 'pending',
            'approved_at' => null,
            'deadline' => now()->subDay()->toDateString(),
        ]);

        $this->actingAs($admin)
            ->put(route('admin.jobs.update', $job), ['status' => 'active'])
            ->assertUnprocessable();
    }

    public function test_withdrawn_application_cannot_be_updated_by_employer(): void
    {
        $employer = User::factory()->create([
            'role' => 'employer',
            'status' => 'active',
            'employer_verified_at' => now(),
        ]);
        $seeker = User::factory()->create(['role' => 'job_seeker', 'status' => 'active']);
        $job = $this->jobFor($employer, ['approved_at' => now()]);
        $application = JobApplication::create([
            'job_id' => $job->id,
            'job_seeker_id' => $seeker->id,
            'cover_letter' => 'I am interested in this role.',
            'resume_path' => 'resumes/test.pdf',
            'status' => 'pending',
        ]);

        $this->actingAs($seeker)
            ->put(route('seeker.applications.withdraw', $application), ['withdraw_reason' => 'Accepted another role'])
            ->assertRedirect();

        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => 'withdrawn',
            'withdraw_reason' => 'Accepted another role',
        ]);

        $this->actingAs($employer)
            ->put(route('employer.applications.status', $application), ['status' => 'hired'])
            ->assertUnprocessable();
    }

    public function test_job_application_stores_resume_in_private_disk_and_sends_notifications(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');
        \Illuminate\Support\Facades\Notification::fake();

        $employer = User::factory()->create([
            'role' => 'employer',
            'status' => 'active',
            'employer_verified_at' => now(),
        ]);
        $seeker = User::factory()->create(['role' => 'job_seeker', 'status' => 'active']);
        $job = $this->jobFor($employer, ['approved_at' => now()]);

        $file = \Illuminate\Http\UploadedFile::fake()->create('my_resume.pdf', 300, 'application/pdf');

        $response = $this->actingAs($seeker)->post(route('applications.store', $job), [
            'cover_letter' => 'Here is my detailed cover letter.',
            'resume' => $file,
        ]);

        $response->assertRedirect();
        $application = JobApplication::where('job_id', $job->id)->where('job_seeker_id', $seeker->id)->first();
        $this->assertNotNull($application);

        \Illuminate\Support\Facades\Storage::disk('local')->assertExists($application->resume_path);

        \Illuminate\Support\Facades\Notification::assertSentTo($seeker, \App\Notifications\ApplicationSubmittedNotification::class);
        \Illuminate\Support\Facades\Notification::assertSentTo($employer, \App\Notifications\NewApplicationReceivedNotification::class);
    }

    public function test_secure_resume_download_authorization_matrix(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');

        $employer = User::factory()->create([
            'role' => 'employer',
            'status' => 'active',
            'employer_verified_at' => now(),
        ]);
        $otherEmployer = User::factory()->create([
            'role' => 'employer',
            'status' => 'active',
            'employer_verified_at' => now(),
        ]);
        $seeker = User::factory()->create(['role' => 'job_seeker', 'status' => 'active']);
        $otherSeeker = User::factory()->create(['role' => 'job_seeker', 'status' => 'active']);
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $job = $this->jobFor($employer, ['approved_at' => now()]);

        $path = 'resumes/private_resume.pdf';
        \Illuminate\Support\Facades\Storage::disk('local')->put($path, 'Sample resume binary content');

        $application = JobApplication::create([
            'job_id' => $job->id,
            'job_seeker_id' => $seeker->id,
            'cover_letter' => 'Test application',
            'resume_path' => $path,
            'status' => 'pending',
        ]);

        // 1. Guest is redirected to login
        $this->get(route('applications.resume', $application))->assertRedirect(route('login'));

        // 2. The applicant CAN download
        $this->actingAs($seeker)
            ->get(route('applications.resume', $application))
            ->assertOk();

        // 3. The job\'s employer CAN download
        $this->actingAs($employer)
            ->get(route('applications.resume', $application))
            ->assertOk();

        // 4. Admin CAN download
        $this->actingAs($admin)
            ->get(route('applications.resume', $application))
            ->assertOk();

        // 5. Unauthorized seeker gets 403 Forbidden
        $this->actingAs($otherSeeker)
            ->get(route('applications.resume', $application))
            ->assertForbidden();

        // 6. Another employer gets 403 Forbidden
        $this->actingAs($otherEmployer)
            ->get(route('applications.resume', $application))
            ->assertForbidden();
    }

    public function test_employer_can_promote_job_to_featured(): void
    {
        $employer = User::factory()->create([
            'role' => 'employer',
            'status' => 'active',
            'employer_verified_at' => now(),
        ]);
        $job = $this->jobFor($employer, ['approved_at' => now()]);

        $this->actingAs($employer)
            ->post(route('employer.jobs.promote', $job))
            ->assertRedirect();

        $this->assertTrue($job->fresh()->is_featured);
    }

    public function test_in_app_chat_and_interview_scheduling(): void
    {
        $employer = User::factory()->create([
            'role' => 'employer',
            'status' => 'active',
            'employer_verified_at' => now(),
            'company_name' => 'Acme Tech',
        ]);
        $seeker = User::factory()->create(['role' => 'job_seeker', 'status' => 'active']);
        $job = $this->jobFor($employer, ['approved_at' => now()]);

        $application = JobApplication::create([
            'job_id' => $job->id,
            'job_seeker_id' => $seeker->id,
            'cover_letter' => 'Skilled in Laravel and MySQL',
            'resume_path' => 'resumes/test.txt',
            'status' => 'pending',
            'ai_match_score' => 88,
        ]);

        // Seeker sends a message
        $this->actingAs($seeker)
            ->post(route('applications.messages.store', $application), ['message' => 'Hello, excited for this opportunity!'])
            ->assertRedirect();

        $this->assertDatabaseHas('application_messages', [
            'job_application_id' => $application->id,
            'sender_id' => $seeker->id,
            'message' => 'Hello, excited for this opportunity!',
        ]);

        // Employer schedules interview
        $this->actingAs($employer)
            ->post(route('employer.applications.interview', $application), [
                'scheduled_at' => now()->addDays(2)->format('Y-m-d\TH:i'),
                'location_type' => 'remote',
                'meeting_link' => 'https://meet.google.com/xyz',
                'notes' => 'Technical screening discussion.',
            ])
            ->assertRedirect();

        $this->assertEquals('interview', $application->fresh()->status);
        $this->assertDatabaseHas('interviews', [
            'job_application_id' => $application->id,
            'location_type' => 'remote',
            'meeting_link' => 'https://meet.google.com/xyz',
        ]);
    }

    public function test_company_showcase_page_is_publicly_accessible(): void
    {
        $employer = User::factory()->create([
            'role' => 'employer',
            'status' => 'active',
            'company_name' => 'Colombo Cloud Solutions',
            'company_slug' => 'colombo-cloud-solutions',
            'bio' => 'Enterprise cloud software builders.',
        ]);

        $this->get(route('companies.show', $employer))
            ->assertOk()
            ->assertSee('Colombo Cloud Solutions')
            ->assertSee('Enterprise cloud software builders.');
    }

    private function jobFor(User $employer, array $overrides = []): Job
    {
        return Job::create(array_merge([
            'employer_id' => $employer->id,
            'title' => 'Software Engineer',
            'main_category' => 'IT',
            'sub_category' => 'Development',
            'location' => 'Colombo',
            'type' => 'Full-time',
            'salary' => 'Rs 250,000',
            'description' => 'Build and maintain Laravel based business applications.',
            'requirements' => 'Laravel, SQL, testing, communication.',
            'responsibilities' => 'Develop features and review code.',
            'deadline' => now()->addWeek()->toDateString(),
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => null,
        ], $overrides));
    }
}
