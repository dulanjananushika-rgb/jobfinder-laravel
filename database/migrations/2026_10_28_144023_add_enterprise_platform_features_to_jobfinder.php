<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Expand users table for Seeker CV Profile and Employer Showcase
        Schema::table('users', function (Blueprint $table) {
            $table->string('headline')->nullable()->after('bio');
            $table->text('skills')->nullable()->after('headline');
            $table->text('experience')->nullable()->after('skills');
            $table->text('education')->nullable()->after('experience');
            $table->string('portfolio_url')->nullable()->after('website');
            $table->string('github_url')->nullable()->after('portfolio_url');
            $table->string('linkedin_url')->nullable()->after('github_url');
            $table->string('google_id')->nullable()->after('remember_token');
            $table->string('linkedin_id')->nullable()->after('google_id');
            $table->string('company_slug')->nullable()->after('company_name');
            $table->string('company_banner')->nullable()->after('company_slug');
            $table->string('company_size')->nullable()->after('company_banner');
            $table->text('company_perks')->nullable()->after('company_size');
        });

        // 2. Expand jobs table for monetization and views analytics
        Schema::table('jobs', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('status');
            $table->timestamp('featured_until')->nullable()->after('is_featured');
            $table->unsignedInteger('views_count')->default(0)->after('featured_until');
        });

        // 3. Expand job_applications for AI Matching and 1-Click Profile Apply
        Schema::table('job_applications', function (Blueprint $table) {
            $table->unsignedTinyInteger('ai_match_score')->nullable()->after('status');
            $table->text('ai_summary')->nullable()->after('ai_match_score');
            $table->string('apply_type')->default('upload')->after('ai_summary'); // 'upload' or 'profile'
        });

        // 4. In-App Direct Chat between Employer and Candidate
        Schema::create('application_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained('job_applications')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // 5. Direct Interview Scheduling
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained('job_applications')->cascadeOnDelete();
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('scheduled_at');
            $table->string('meeting_link')->nullable();
            $table->string('location_type')->default('remote'); // remote, in_person
            $table->text('notes')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
        Schema::dropIfExists('application_messages');

        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['ai_match_score', 'ai_summary', 'apply_type']);
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'featured_until', 'views_count']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'headline', 'skills', 'experience', 'education',
                'portfolio_url', 'github_url', 'linkedin_url',
                'google_id', 'linkedin_id',
                'company_slug', 'company_banner', 'company_size', 'company_perks'
            ]);
        });
    }
};
