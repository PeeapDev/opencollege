<?php

namespace App\Modules\Core\Traits;

use App\Modules\Settings\Models\Institution;

trait BelongsToInstitution
{
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    protected static function bootBelongsToInstitution(): void
    {
        static::creating(function ($model) {
            if (empty($model->institution_id) && auth()->check()) {
                $model->institution_id = auth()->user()->current_institution_id;
            }
        });
    }
}
