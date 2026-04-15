<?php

namespace App\Modules\Staff\Models;

use Illuminate\Database\Eloquent\Model;

class TeachingAssignment extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['is_primary' => 'boolean'];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
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
