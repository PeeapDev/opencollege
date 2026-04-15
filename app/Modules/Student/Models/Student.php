<?php

namespace App\Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\BelongsToInstitution;

class Student extends Model
{
    use BelongsToInstitution;

    protected $guarded = ['id'];

    protected $casts = [
        'admission_date' => 'date',
        'expected_graduation' => 'date',
        'actual_graduation' => 'date',
        'date_of_birth' => 'date',
        'previous_education' => 'array',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function program()
    {
        return $this->belongsTo(\App\Modules\Academic\Models\Program::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function grades()
    {
        return $this->hasMany(\App\Modules\Exam\Models\Grade::class);
    }

    public function invoices()
    {
        return $this->hasMany(\App\Modules\Finance\Models\Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(\App\Modules\Finance\Models\Payment::class);
    }

    public function attendances()
    {
        return $this->hasMany(\App\Modules\Attendance\Models\Attendance::class);
    }

    public function cgpaRecords()
    {
        return $this->hasMany(\App\Modules\Exam\Models\CgpaRecord::class);
    }

    public function idCard()
    {
        return $this->hasOne(\App\Modules\Student\Models\IdCard::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->user->name ?? '';
    }
}
