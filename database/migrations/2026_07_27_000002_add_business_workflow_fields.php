<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('employer_verified_at')->nullable()->after('status');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('status');
            $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable()->after('approved_by');
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->timestamp('withdrawn_at')->nullable()->after('status');
            $table->text('withdraw_reason')->nullable()->after('withdrawn_at');
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject');
            $table->text('message');
            $table->string('status')->default('new')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');

        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['withdrawn_at', 'withdraw_reason']);
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['approved_at', 'rejection_reason']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('employer_verified_at');
        });
    }
};
