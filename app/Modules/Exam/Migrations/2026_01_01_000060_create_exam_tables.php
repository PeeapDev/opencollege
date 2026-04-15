<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Mid-Semester, Final, Supplementary, Resit
            $table->decimal('weight_percentage', 5, 2)->default(100); // e.g. 30% for mid-sem, 70% for final
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->date('exam_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('venue')->nullable();
            $table->decimal('total_marks', 6, 2)->default(100);
            $table->decimal('pass_marks', 6, 2)->default(50);
            $table->boolean('published')->default(false);
            $table->timestamps();
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('marks_obtained', 6, 2)->nullable();
            $table->string('letter_grade', 5)->nullable(); // A, B+, B, C+, C, D, F
            $table->decimal('grade_point', 4, 2)->nullable(); // 4.0, 3.5, 3.0, etc.
            $table->text('remarks')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['exam_id', 'student_id']);
        });

        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('letter_grade', 5);
            $table->decimal('min_percentage', 5, 2);
            $table->decimal('max_percentage', 5, 2);
            $table->decimal('grade_point', 4, 2);
            $table->string('description', 50)->nullable(); // Excellent, Very Good, Good, etc.
            $table->timestamps();
        });

        Schema::create('cgpa_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->decimal('semester_gpa', 4, 2);
            $table->decimal('cumulative_gpa', 4, 2);
            $table->integer('total_credits_attempted');
            $table->integer('total_credits_earned');
            $table->string('academic_standing', 30)->nullable(); // Good Standing, Probation, Dean's List
            $table->timestamps();
            $table->unique(['student_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cgpa_records');
        Schema::dropIfExists('grade_scales');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('exam_types');
    }
};
