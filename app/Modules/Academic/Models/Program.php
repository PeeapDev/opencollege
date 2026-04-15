<?php

namespace App\Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\BelongsToInstitution;

class Program extends Model
{
    use BelongsToInstitution;

    protected $guarded = ['id'];
    protected $casts = ['active' => 'boolean'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'program_courses')
            ->withPivot('year_level', 'semester_number', 'is_required')
            ->withTimestamps();
    }

    public function students()
    {
        return $this->hasMany(\App\Modules\Student\Models\Student::class);
    }
}
