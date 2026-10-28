<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
            $table->decimal('min_salary', 12, 2)->nullable()->after('salary');
            $table->decimal('max_salary', 12, 2)->nullable()->after('min_salary');
            $table->string('salary_currency', 10)->default('LKR')->after('max_salary');
            $table->string('salary_period', 20)->default('monthly')->after('salary_currency');

            $table->index(['status', 'main_category', 'location']);
            $table->index(['status', 'type']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex(['status', 'main_category', 'location']);
            $table->dropIndex(['status', 'type']);
            $table->dropIndex(['slug']);
            $table->dropColumn(['slug', 'min_salary', 'max_salary', 'salary_currency', 'salary_period']);
        });
    }
};
