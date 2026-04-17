<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DPG Criterion 9 safety-net migration.
 *
 * - Creates audit_logs table (append-only audit trail)
 * - Adds must_change_password flag to users (force change on first login)
 * - Adds deleted_at to institutions (soft-delete, prevents accidental destruction)
 * - Adds login throttle / lockout columns on users (failed_login_attempts, locked_until)
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action', 20)->index();
                $table->string('model_type', 120)->index();
                $table->unsignedBigInteger('model_id')->nullable()->index();
                $table->json('before')->nullable();
                $table->json('after')->nullable();
                $table->string('ip_address', 64)->nullable();
                $table->string('user_agent', 500)->nullable();
                $table->timestamps();
                $table->index(['model_type', 'model_id']);
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table): void {
                if (!Schema::hasColumn('users', 'must_change_password')) {
                    $table->boolean('must_change_password')->default(false)->after('password');
                }
                if (!Schema::hasColumn('users', 'failed_login_attempts')) {
                    $table->unsignedSmallInteger('failed_login_attempts')->default(0);
                }
                if (!Schema::hasColumn('users', 'locked_until')) {
                    $table->timestamp('locked_until')->nullable();
                }
                if (!Schema::hasColumn('users', 'last_login_at')) {
                    $table->timestamp('last_login_at')->nullable();
                }
                if (!Schema::hasColumn('users', 'last_login_ip')) {
                    $table->string('last_login_ip', 64)->nullable();
                }
            });
        }

        if (Schema::hasTable('institutions') && !Schema::hasColumn('institutions', 'deleted_at')) {
            Schema::table('institutions', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table): void {
                foreach (['must_change_password', 'failed_login_attempts', 'locked_until', 'last_login_at', 'last_login_ip'] as $col) {
                    if (Schema::hasColumn('users', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('institutions') && Schema::hasColumn('institutions', 'deleted_at')) {
            Schema::table('institutions', function (Blueprint $table): void {
                $table->dropSoftDeletes();
            });
        }
    }
};
