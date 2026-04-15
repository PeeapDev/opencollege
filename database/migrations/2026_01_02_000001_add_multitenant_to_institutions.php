<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->string('custom_domain')->nullable()->unique()->after('domain');
            $table->string('type', 30)->default('college')->after('code'); // college, polytechnic, university
            $table->string('registration_number', 50)->nullable()->after('type');
            $table->text('description')->nullable()->after('name');
            $table->string('accreditation_status', 30)->default('pending')->after('active'); // pending, accredited, probation, revoked
            $table->date('subscription_start')->nullable()->after('accreditation_status');
            $table->date('subscription_end')->nullable()->after('subscription_start');
            $table->string('plan', 20)->default('free')->after('subscription_end'); // free, basic, premium, enterprise
            $table->integer('max_students')->default(500)->after('plan');
            $table->integer('max_staff')->default(100)->after('max_students');
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn(['custom_domain', 'type', 'registration_number', 'description', 'accreditation_status', 'subscription_start', 'subscription_end', 'plan', 'max_students', 'max_staff']);
        });
    }
};
