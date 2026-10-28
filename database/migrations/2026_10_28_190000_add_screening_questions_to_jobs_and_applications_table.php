<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->json('screening_questions')->nullable()->after('responsibilities');
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->json('screening_answers')->nullable()->after('cover_letter');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn('screening_answers');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('screening_questions');
        });
    }
};
