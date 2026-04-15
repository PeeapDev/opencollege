<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add NSI number to students table
        Schema::table('students', function (Blueprint $table) {
            $table->string('nsi_number', 30)->nullable()->after('student_id');
            $table->string('religion', 50)->nullable()->after('nationality');
            $table->string('marital_status', 20)->nullable()->after('religion');
            $table->string('guardian_name')->nullable()->after('emergency_contact_relation');
            $table->string('guardian_phone', 30)->nullable()->after('guardian_name');
            $table->string('guardian_relation', 50)->nullable()->after('guardian_phone');
            $table->string('guardian_email')->nullable()->after('guardian_relation');
        });

        // Add phone/username to users for multi-login
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->unique()->after('email');
        });

        // Online Admissions
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('application_number', 30)->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('nationality', 50)->default('Sierra Leonean');
            $table->string('national_id', 50)->nullable();
            $table->string('nsi_number', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->default('Sierra Leone');
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->string('photo')->nullable();
            $table->json('documents')->nullable();
            $table->json('previous_education')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone', 30)->nullable();
            $table->string('guardian_email')->nullable();
            $table->string('guardian_relation', 50)->nullable();
            $table->enum('status', ['pending', 'under_review', 'accepted', 'rejected', 'waitlisted', 'enrolled'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('academic_year', 20)->nullable();
            $table->timestamps();
        });

        // Admission settings per institution
        Schema::create('admission_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_open')->default(false);
            $table->date('open_date')->nullable();
            $table->date('close_date')->nullable();
            $table->string('academic_year', 20)->nullable();
            $table->text('instructions')->nullable();
            $table->text('requirements')->nullable();
            $table->json('required_documents')->nullable();
            $table->decimal('application_fee', 10, 2)->default(0);
            $table->boolean('require_nsi')->default(false);
            $table->timestamps();
        });

        // Student ID cards
        Schema::create('id_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('card_number', 30)->unique();
            $table->string('qr_code')->nullable();
            $table->string('barcode')->nullable();
            $table->date('issued_date');
            $table->date('expiry_date');
            $table->enum('status', ['active', 'expired', 'revoked', 'lost'])->default('active');
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Frontend settings per institution
        Schema::create('frontend_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->boolean('website_enabled')->default(true);
            $table->string('template', 50)->default('classic');
            $table->string('hero_title')->nullable();
            $table->string('hero_subtitle')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('hero_cta_text')->nullable();
            $table->string('hero_cta_link')->nullable();
            $table->text('about_text')->nullable();
            $table->string('about_image')->nullable();
            $table->json('features')->nullable();
            $table->json('stats')->nullable();
            $table->json('gallery_images')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('contact_address')->nullable();
            $table->json('social_links')->nullable();
            $table->string('primary_color', 10)->default('#2563eb');
            $table->string('secondary_color', 10)->default('#1e40af');
            $table->string('footer_text')->nullable();
            $table->timestamps();
        });

        // Exam schedules
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room', 50)->nullable();
            $table->string('invigilator')->nullable();
            $table->boolean('published')->default(false);
            $table->timestamps();
        });

        // Published results
        Schema::create('result_publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('year_level')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_publications');
        Schema::dropIfExists('exam_schedules');
        Schema::dropIfExists('frontend_settings');
        Schema::dropIfExists('id_cards');
        Schema::dropIfExists('admission_settings');
        Schema::dropIfExists('admissions');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['nsi_number', 'religion', 'marital_status', 'guardian_name', 'guardian_phone', 'guardian_relation', 'guardian_email']);
        });
    }
};
