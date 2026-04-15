<?php

namespace App\Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\BelongsToInstitution;

class Course extends Model
{
    use BelongsToInstitution;

    protected $guarded = ['id'];
    protected $casts = ['active' => 'boolean'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_courses')
            ->withPivot('year_level', 'semester_number', 'is_required')
            ->withTimestamps();
    }

    public function sections()
    {
        return $this->hasMany(CourseSection::class);
    }
}
