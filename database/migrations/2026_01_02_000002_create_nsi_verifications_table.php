<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nsi_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nsi_number', 30)->index();
            $table->string('student_name')->nullable();
            $table->string('high_school_name')->nullable();
            $table->string('high_school_code', 20)->nullable();
            $table->string('graduation_year', 10)->nullable();
            $table->json('subjects_results')->nullable();
            $table->decimal('aggregate_score', 5, 2)->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'failed', 'not_found'])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->text('api_response')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nsi_verifications');
    }
};
