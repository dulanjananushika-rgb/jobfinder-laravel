<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\JobReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SafetyAndAlertsTest extends TestCase
{
    use RefreshDatabase;

    private function createEmployer(): User
    {
        return User::factory()->create([
            'role' => 'employer',
            'status' => 'active',
            'employer_verified_at' => now(),
            'company_name' => 'Apex Solutions',
        ]);
    }

    public function test_user_can_report_suspicious_job_and_admin_can_resolve()
    {
        $employer = $this->createEmployer();
        $job = Job::create([
            'employer_id' => $employer->id,
            'title' => 'Suspicious Fast Money Manager',
            'main_category' => 'Business',
            'location' => 'Colombo',
            'type' => 'Full-time',
            'description' => 'Earn Rs 50,000 daily with zero effort. Send deposit first.',
            'requirements' => 'Phone and bank account',
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $seeker = User::factory()->create(['role' => 'job_seeker', 'status' => 'active']);

        $response = $this->actingAs($seeker)->post(route('jobs.report', $job), [
            'reason' => 'fraud_or_scam',
            'details' => 'This job listing requires candidates to deposit money into a personal bank account before an interview.',
        ]);

        $response->assertSessionHas('status');

        $this->assertDatabaseHas('job_reports', [
            'job_id' => $job->id,
            'user_id' => $seeker->id,
            'reason' => 'fraud_or_scam',
            'status' => 'pending',
        ]);

        $report = JobReport::first();

        // Admin checks and resolves report
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $adminResponse = $this->actingAs($admin)->get(route('admin.reports'));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Suspicious Fast Money Manager');
        $adminResponse->assertSee('Fraud or scam');

        $updateResponse = $this->actingAs($admin)->put(route('admin.reports.update', $report), [
            'status' => 'resolved',
            'admin_notes' => 'Job investigated and employer account warned.',
        ]);

        $updateResponse->assertSessionHas('status');
        $this->assertEquals('resolved', $report->fresh()->status);
    }

    public function test_candidate_can_activate_email_job_alert()
    {
        $response = $this->post(route('job-alerts.store'), [
            'email' => 'dev@colombo.test',
            'category' => 'Technology',
            'location' => 'Colombo',
            'keyword' => 'Laravel',
        ]);

        $response->assertSessionHas('status');

        $this->assertDatabaseHas('job_alerts', [
            'email' => 'dev@colombo.test',
            'category' => 'Technology',
            'location' => 'Colombo',
            'keyword' => 'Laravel',
            'is_active' => true,
        ]);
    }
}
