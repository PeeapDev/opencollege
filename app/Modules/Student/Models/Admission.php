<?php

namespace App\Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\BelongsToInstitution;

class Admission extends Model
{
    use BelongsToInstitution;

    protected $guarded = ['id'];

    protected $casts = [
        'date_of_birth' => 'date',
        'documents' => 'array',
        'previous_education' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function program()
    {
        return $this->belongsTo(\App\Modules\Academic\Models\Program::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewed_by');
    }
}
