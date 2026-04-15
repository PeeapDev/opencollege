<?php

namespace App\Modules\Exam\Models;

use Illuminate\Database\Eloquent\Model;

class CgpaRecord extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'semester_gpa' => 'decimal:2',
        'cumulative_gpa' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(\App\Modules\Student\Models\Student::class);
    }

    public function semester()
    {
        return $this->belongsTo(\App\Modules\Academic\Models\Semester::class);
    }
}
