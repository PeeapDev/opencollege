<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payroll_runs')) {
            Schema::create('payroll_runs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('institution_id');
                $table->string('month', 20);
                $table->integer('year');
                $table->integer('total_staff')->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->enum('status', ['draft', 'processed', 'paid', 'cancelled'])->default('draft');
                $table->unsignedBigInteger('processed_by')->nullable();
                $table->timestamps();
                $table->index(['institution_id', 'year', 'month']);
            });
        }

        if (!Schema::hasTable('payroll_items')) {
            Schema::create('payroll_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('payroll_run_id');
                $table->unsignedBigInteger('staff_id');
                $table->decimal('basic_salary', 12, 2)->default(0);
                $table->decimal('allowances', 12, 2)->default(0);
                $table->decimal('deductions', 12, 2)->default(0);
                $table->decimal('net_salary', 12, 2)->default(0);
                $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
                $table->string('payment_method')->nullable();
                $table->string('payment_reference')->nullable();
                $table->timestamps();
                $table->foreign('payroll_run_id')->references('id')->on('payroll_runs')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payroll_runs');
    }
};
