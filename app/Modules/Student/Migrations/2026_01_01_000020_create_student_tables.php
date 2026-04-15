<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('student_id', 30)->unique(); // Matric/Reg number
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->integer('current_year')->default(1);
            $table->integer('current_semester')->default(1);
            $table->enum('status', ['active', 'suspended', 'withdrawn', 'graduated', 'deferred'])->default('active');
            $table->date('admission_date');
            $table->date('expected_graduation')->nullable();
            $table->date('actual_graduation')->nullable();
            $table->string('photo')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('nationality', 50)->default('Sierra Leonean');
            $table->string('national_id', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();
            $table->string('emergency_contact_relation', 50)->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->json('previous_education')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['enrolled', 'dropped', 'completed', 'failed', 'incomplete'])->default('enrolled');
            $table->date('enrolled_at');
            $table->date('dropped_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'course_section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('students');
    }
};
