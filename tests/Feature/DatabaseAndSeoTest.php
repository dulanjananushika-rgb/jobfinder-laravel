<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatabaseAndSeoTest extends TestCase
{
    use RefreshDatabase;

    private function createEmployer(): User
    {
        return User::factory()->create([
            'role' => 'employer',
            'status' => 'active',
            'employer_verified_at' => now(),
            'company_name' => 'Acme Global',
        ]);
    }

    public function test_soft_deletes_preserve_records_in_database()
    {
        $employer = $this->createEmployer();
        $job = Job::create([
            'employer_id' => $employer->id,
            'title' => 'Software Engineer',
            'main_category' => 'Technology',
            'location' => 'Colombo',
            'type' => 'Full-time',
            'description' => 'Role description goes here in full text',
            'requirements' => 'Skills required',
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $seeker = User::factory()->create(['role' => 'job_seeker']);
        $application = JobApplication::create([
            'job_id' => $job->id,
            'job_seeker_id' => $seeker->id,
            'cover_letter' => 'Test application letter',
            'resume_path' => 'resumes/sample.pdf',
            'status' => 'pending',
        ]);

        // Delete job
        $job->delete();
        $this->assertSoftDeleted('jobs', ['id' => $job->id]);

        // Delete application
        $application->delete();
        $this->assertSoftDeleted('job_applications', ['id' => $application->id]);
    }

    public function test_force_deleted_application_removes_resume_file_from_storage()
    {
        Storage::fake('local');
        $fakePath = 'resumes/candidate_resume.pdf';
        Storage::disk('local')->put($fakePath, 'Test resume content');

        $employer = $this->createEmployer();
        $job = Job::create([
            'employer_id' => $employer->id,
            'title' => 'QA Engineer',
            'main_category' => 'Technology',
            'location' => 'Colombo',
            'type' => 'Full-time',
            'description' => 'QA role description',
            'requirements' => 'Skills required',
            'status' => 'active',
            'approved_at' => now(),
        ]);
        $seeker = User::factory()->create(['role' => 'job_seeker']);

        $application = JobApplication::create([
            'job_id' => $job->id,
            'job_seeker_id' => $seeker->id,
            'cover_letter' => 'Test letter',
            'resume_path' => $fakePath,
            'status' => 'pending',
        ]);

        $this->assertTrue(Storage::disk('local')->exists($fakePath));

        // Soft delete should NOT remove the file yet
        $application->delete();
        $this->assertTrue(Storage::disk('local')->exists($fakePath));

        // Force delete should trigger automated cleanup
        $application->forceDelete();
        $this->assertFalse(Storage::disk('local')->exists($fakePath));
    }

    public function test_job_generates_slug_and_can_be_accessed_via_slug()
    {
        $employer = $this->createEmployer();
        $job = Job::create([
            'employer_id' => $employer->id,
            'title' => 'Senior Laravel Cloud Architect',
            'main_category' => 'Technology',
            'location' => 'Colombo',
            'type' => 'Full-time',
            'description' => 'Detailed role description for cloud architect',
            'requirements' => '7+ years Laravel experience',
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $this->assertNotNull($job->slug);
        $this->assertEquals('senior-laravel-cloud-architect', $job->slug);

        $response = $this->get(route('jobs.show', $job->slug));
        $response->assertStatus(200);
        $response->assertSee('Senior Laravel Cloud Architect');
    }

    public function test_numeric_salary_filtering()
    {
        $employer = $this->createEmployer();

        $jobHigh = Job::create([
            'employer_id' => $employer->id,
            'title' => 'High Salary Executive',
            'main_category' => 'Technology',
            'location' => 'Colombo',
            'type' => 'Full-time',
            'description' => 'Exec role',
            'requirements' => 'Requirements',
            'min_salary' => 300000,
            'max_salary' => 500000,
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $jobLow = Job::create([
            'employer_id' => $employer->id,
            'title' => 'Junior Trainee',
            'main_category' => 'Technology',
            'location' => 'Colombo',
            'type' => 'Full-time',
            'description' => 'Junior role',
            'requirements' => 'Requirements',
            'min_salary' => 50000,
            'max_salary' => 80000,
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $response = $this->get(route('jobs.index', ['min_salary' => 250000]));
        $response->assertStatus(200);
        $response->assertSee('High Salary Executive');
        $response->assertDontSee('Junior Trainee');
    }
}
