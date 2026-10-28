<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoAndSocialTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_page_has_schema_json_ld_and_open_graph_meta_tags()
    {
        $employer = User::factory()->create([
            'role' => 'employer',
            'status' => 'active',
            'employer_verified_at' => now(),
            'company_name' => 'Virtusa Sri Lanka',
            'website' => 'https://virtusa.com',
        ]);

        $job = Job::create([
            'employer_id' => $employer->id,
            'title' => 'Lead DevOps Architect',
            'main_category' => 'Technology',
            'location' => 'Colombo',
            'type' => 'Full-time',
            'salary' => 'Rs 450,000 / month',
            'min_salary' => 400000,
            'max_salary' => 500000,
            'description' => 'We are seeking a Lead DevOps Architect to scale Kubernetes clusters.',
            'requirements' => 'AWS, Terraform, Kubernetes',
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $response = $this->get(route('jobs.show', $job->slug));
        $response->assertStatus(200);

        // Assert Open Graph tags
        $response->assertSee('property="og:title"', false);
        $response->assertSee('Virtusa Sri Lanka', false);
        $response->assertSee('property="og:type" content="article"', false);

        // Assert Google for Jobs Schema.org JSON-LD
        $response->assertSee('"@type": "JobPosting"', false);
        $response->assertSee('"title": "Lead DevOps Architect"', false);
        $response->assertSee('"addressLocality": "Colombo"', false);
        $response->assertSee('"addressCountry": "LK"', false);

        // Assert Social Sharing buttons
        $response->assertSee('Share via WhatsApp');
        $response->assertSee('Share on LinkedIn');
        $response->assertSee('Copy Link');
    }
}
