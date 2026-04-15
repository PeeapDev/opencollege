<?php

namespace App\Modules\Exam\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\BelongsToInstitution;

class Exam extends Model
{
    use BelongsToInstitution;

    protected $guarded = ['id'];

    protected $casts = [
        'exam_date' => 'date',
        'total_marks' => 'decimal:2',
        'pass_marks' => 'decimal:2',
        'published' => 'boolean',
    ];

    public function courseSection()
    {
        return $this->belongsTo(\App\Modules\Academic\Models\CourseSection::class);
    }

    public function examType()
    {
        return $this->belongsTo(ExamType::class);
    }

    public function semester()
    {
        return $this->belongsTo(\App\Modules\Academic\Models\Semester::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
