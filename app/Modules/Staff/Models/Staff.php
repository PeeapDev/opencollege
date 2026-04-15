<?php

namespace App\Modules\Staff\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\BelongsToInstitution;

class Staff extends Model
{
    use BelongsToInstitution;

    protected $table = 'staff';
    protected $guarded = ['id'];

    protected $casts = [
        'joining_date' => 'date',
        'leaving_date' => 'date',
        'date_of_birth' => 'date',
        'basic_salary' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function department()
    {
        return $this->belongsTo(\App\Modules\Academic\Models\Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function teachingAssignments()
    {
        return $this->hasMany(TeachingAssignment::class);
    }

    public function attendances()
    {
        return $this->hasMany(\App\Modules\Attendance\Models\StaffAttendance::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->user->name ?? '';
    }
}
