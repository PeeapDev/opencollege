<?php

namespace App\Modules\Exam\Models;

use App\Traits\LogsAudit;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use LogsAudit;

    protected $guarded = ['id'];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'grade_point' => 'decimal:2',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(\App\Modules\Student\Models\Student::class);
    }
}
