<?php

namespace App\Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\BelongsToInstitution;

class CourseSection extends Model
{
    use BelongsToInstitution;

    protected $guarded = ['id'];
    protected $casts = ['active' => 'boolean'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(\App\Models\User::class, 'lecturer_id');
    }

    public function enrollments()
    {
        return $this->hasMany(\App\Modules\Student\Models\Enrollment::class);
    }

    public function students()
    {
        return $this->hasManyThrough(
            \App\Modules\Student\Models\Student::class,
            \App\Modules\Student\Models\Enrollment::class,
            'course_section_id',
            'id',
            'id',
            'student_id'
        );
    }
}
