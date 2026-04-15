<?php

namespace App\Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'enrolled_at' => 'date',
        'dropped_at' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function courseSection()
    {
        return $this->belongsTo(\App\Modules\Academic\Models\CourseSection::class);
    }

    public function semester()
    {
        return $this->belongsTo(\App\Modules\Academic\Models\Semester::class);
    }
}
