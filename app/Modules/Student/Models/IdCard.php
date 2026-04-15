<?php

namespace App\Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\BelongsToInstitution;

class IdCard extends Model
{
    use BelongsToInstitution;

    protected $guarded = ['id'];

    protected $casts = [
        'issued_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function issuer()
    {
        return $this->belongsTo(\App\Models\User::class, 'issued_by');
    }
}
