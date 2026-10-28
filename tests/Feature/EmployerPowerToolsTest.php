<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployerPowerToolsTest extends TestCase
{
    use RefreshDatabase;

    private function createEmployer(): User
    {
        return User::factory()->create([
            'role' => 'employer',
            'status' => 'active',
            'employer_verified_at' => now(),
            'company_name' => 'WSO2 Sri Lanka',
        ]);
    }

    public function test_employer_can_export_candidates_to_csv()
    {
        $employer = $this->createEmployer();
        $job = Job::create([
            'employer_id' => $employer->id,
            'title' => 'API Integration Engineer',
            'main_category' => 'Technology',
            'location' => 'Colombo',
            'type' => 'Full-time',
            'description' => 'Role description in full text',
            'requirements' => 'Java, Ballerina, Cloud',
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $candidate1 = User::factory()->create(['name' => 'Kamal Perera', 'email' => 'kamal@test.lk', 'role' => 'job_seeker']);
        $candidate2 = User::factory()->create(['name' => 'Nimal Silva', 'email' => 'nimal@test.lk', 'role' => 'job_seeker']);

        JobApplication::create([
            'job_id' => $job->id,
            'job_seeker_id' => $candidate1->id,
            'cover_letter' => 'Passionate about API gateway architecture',
            'resume_path' => 'resumes/kamal.pdf',
            'status' => 'shortlisted',
            'ai_match_score' => 88,
        ]);

        JobApplication::create([
            'job_id' => $job->id,
            'job_seeker_id' => $candidate2->id,
            'cover_letter' => 'Senior cloud integration specialist',
            'resume_path' => 'resumes/nimal.pdf',
            'status' => 'pending',
            'ai_match_score' => 74,
        ]);

        $response = $this->actingAs($employer)->get(route('employer.applications.export'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Application ID', $content);
        $this->assertStringContainsString('Kamal Perera', $content);
        $this->assertStringContainsString('kamal@test.lk', $content);
        $this->assertStringContainsString('Nimal Silva', $content);
        $this->assertStringContainsString('Shortlisted', $content);
        $this->assertStringContainsString('API Integration Engineer', $content);
    }

    public function test_job_screening_questions_and_candidate_answers()
    {
        Storage::fake('local');
        $employer = $this->createEmployer();

        $job = Job::create([
            'employer_id' => $employer->id,
            'title' => 'Frontend React Engineer',
            'main_category' => 'Technology',
            'location' => 'Colombo',
            'type' => 'Full-time',
            'description' => 'Build high-performance React web applications.',
            'requirements' => 'React, TypeScript, CSS',
            'screening_questions' => [
                'How many years of experience do you have with TypeScript?',
                'What is your earliest possible joining date?',
            ],
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $candidate = User::factory()->create(['role' => 'job_seeker', 'status' => 'active']);

        $response = $this->actingAs($candidate)->post(route('applications.store', $job), [
            'cover_letter' => 'I have 4 years of solid enterprise experience with TypeScript and React.',
            'resume' => UploadedFile::fake()->create('cv.pdf', 300, 'application/pdf'),
            'screening_answers' => [
                0 => '4 years of intensive commercial TypeScript',
                1 => 'Immediate / 1 week notice',
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('job_applications', [
            'job_id' => $job->id,
            'job_seeker_id' => $candidate->id,
        ]);

        $application = JobApplication::where('job_id', $job->id)->first();
        $this->assertNotNull($application->screening_answers);
        $this->assertEquals('4 years of intensive commercial TypeScript', $application->screening_answers[0]);
        $this->assertEquals('Immediate / 1 week notice', $application->screening_answers[1]);
    }
}
