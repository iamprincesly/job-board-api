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
        Schema::create('applications', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_job_id')->constrained('company_jobs')->onDelete('cascade');
            $table->foreignUlid('candidate_id')->constrained()->onDelete('cascade');
            $table->text('cover_letter');
            $table->string('resume_path');
            $table->string('cover_letter_file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
