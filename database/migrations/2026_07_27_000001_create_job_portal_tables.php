<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('main_category')->index();
            $table->string('sub_category')->nullable();
            $table->string('location')->index();
            $table->string('type')->index();
            $table->string('salary')->nullable();
            $table->text('description');
            $table->text('requirements');
            $table->text('responsibilities')->nullable();
            $table->date('deadline')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_seeker_id')->constrained('users')->cascadeOnDelete();
            $table->text('cover_letter');
            $table->string('resume_path');
            $table->string('status')->default('pending')->index();
            $table->timestamps();
            $table->unique(['job_id', 'job_seeker_id']);
        });

        Schema::create('saved_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_seeker_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['job_id', 'job_seeker_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_jobs');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('jobs');
    }
};
