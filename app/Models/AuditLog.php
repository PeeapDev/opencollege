<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AuditLog — DPG Criterion 9.
 *
 * Immutable audit trail of create/update/delete events across the
 * platform. Written by the LogsAudit trait; never edited by the app.
 */
class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'before',
        'after',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'before' => 'array',
        'after'  => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Audit logs are append-only. Block edits at the model level.
     */
    public function save(array $options = [])
    {
        if ($this->exists) {
            throw new \RuntimeException('Audit log entries are immutable');
        }

        return parent::save($options);
    }
}
