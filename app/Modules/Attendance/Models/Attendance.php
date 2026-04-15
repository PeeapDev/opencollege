<?php

namespace App\Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\BelongsToInstitution;

class Attendance extends Model
{
    use BelongsToInstitution;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
    ];

    public function courseSection()
    {
        return $this->belongsTo(\App\Modules\Academic\Models\CourseSection::class);
    }

    public function student()
    {
        return $this->belongsTo(\App\Modules\Student\Models\Student::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'marked_by');
    }
}
