<?php

namespace App\Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\BelongsToInstitution;

class Department extends Model
{
    use BelongsToInstitution;

    protected $guarded = ['id'];
    protected $casts = ['active' => 'boolean'];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function head()
    {
        return $this->belongsTo(\App\Models\User::class, 'head_id');
    }

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function staff()
    {
        return $this->hasMany(\App\Modules\Staff\Models\Staff::class);
    }
}
