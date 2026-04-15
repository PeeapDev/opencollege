<?php

namespace App\Modules\Communication\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\BelongsToInstitution;

class Notice extends Model
{
    use BelongsToInstitution;

    protected $guarded = ['id'];

    protected $casts = [
        'publish_date' => 'date',
        'expiry_date' => 'date',
        'is_pinned' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
