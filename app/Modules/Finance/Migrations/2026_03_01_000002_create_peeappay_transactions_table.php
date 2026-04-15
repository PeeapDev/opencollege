<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('peeappay_transactions')) {
            Schema::create('peeappay_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('institution_id');
                $table->unsignedBigInteger('invoice_id')->nullable();
                $table->unsignedBigInteger('student_id')->nullable();
                $table->string('reference')->unique();
                $table->string('transaction_id')->nullable();
                $table->decimal('amount', 12, 2);
                $table->string('currency', 10)->default('NLE');
                $table->enum('status', ['pending', 'success', 'completed', 'paid', 'failed', 'cancelled', 'refunded'])->default('pending');
                $table->string('channel')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->unsignedBigInteger('initiated_by')->nullable();
                $table->timestamps();
                $table->index(['institution_id', 'status']);
                $table->index('reference');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('peeappay_transactions');
    }
};
