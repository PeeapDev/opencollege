<?php

namespace App\Modules\Settings\Models;

use App\Traits\LogsAudit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institution extends Model
{
    use SoftDeletes;
    use LogsAudit;

    protected $guarded = ['id'];

    protected $casts = [
        'settings' => 'array',
        'active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasManyThrough(
            \App\Models\User::class,
            UserRole::class,
            'institution_id',
            'id',
            'id',
            'user_id'
        );
    }

    public function academicYears()
    {
        return $this->hasMany(\App\Modules\Academic\Models\AcademicYear::class);
    }

    public function currentAcademicYear()
    {
        return $this->hasOne(\App\Modules\Academic\Models\AcademicYear::class)->where('is_current', true);
    }

    public function departments()
    {
        return $this->hasMany(\App\Modules\Academic\Models\Department::class);
    }

    public function faculties()
    {
        return $this->hasMany(\App\Modules\Academic\Models\Faculty::class);
    }

    public function programs()
    {
        return $this->hasMany(\App\Modules\Academic\Models\Program::class);
    }

    public function students()
    {
        return $this->hasMany(\App\Modules\Student\Models\Student::class);
    }

    public function staffMembers()
    {
        return $this->hasMany(\App\Modules\Staff\Models\Staff::class);
    }

    public function courses()
    {
        return $this->hasMany(\App\Modules\Academic\Models\Course::class);
    }
}
