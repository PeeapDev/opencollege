<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

/**
 * Audit logging trait — DPG Criterion 9.
 *
 * Apply to any Eloquent model to log create/update/delete events.
 * Captures who did what, when, and the actual field changes.
 *
 * Usage:
 *   class Grade extends Model { use LogsAudit; }
 *
 * Configure fields to omit (e.g. passwords) by overriding $auditOmit.
 */
trait LogsAudit
{
    protected static array $auditOmitDefaults = ['password', 'remember_token'];

    public static function bootLogsAudit(): void
    {
        static::created(function ($model) {
            $model->writeAudit('created', null, $model->attributesToArrayForAudit());
        });

        static::updated(function ($model) {
            $original = [];
            $changed = [];
            foreach ($model->getChanges() as $key => $newVal) {
                if (in_array($key, array_merge(static::$auditOmitDefaults, $model->auditOmit ?? []), true)) {
                    continue;
                }
                $original[$key] = $model->getOriginal($key);
                $changed[$key] = $newVal;
            }
            if ($changed) {
                $model->writeAudit('updated', $original, $changed);
            }
        });

        static::deleted(function ($model) {
            $model->writeAudit('deleted', $model->attributesToArrayForAudit(), null);
        });
    }

    public function attributesToArrayForAudit(): array
    {
        $data = $this->attributesToArray();
        foreach (array_merge(static::$auditOmitDefaults, $this->auditOmit ?? []) as $k) {
            unset($data[$k]);
        }

        return $data;
    }

    protected function writeAudit(string $action, ?array $before, ?array $after): void
    {
        try {
            AuditLog::create([
                'user_id'      => Auth::id(),
                'action'       => $action,
                'model_type'   => static::class,
                'model_id'     => $this->getKey(),
                'before'       => $before,
                'after'        => $after,
                'ip_address'   => request()?->ip(),
                'user_agent'   => substr((string) request()?->userAgent(), 0, 500),
            ]);
        } catch (\Throwable $e) {
            // Never break the primary operation because of audit failure
            \Log::warning('Audit log write failed: '.$e->getMessage());
        }
    }
}
