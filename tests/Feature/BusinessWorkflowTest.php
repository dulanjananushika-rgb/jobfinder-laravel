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
