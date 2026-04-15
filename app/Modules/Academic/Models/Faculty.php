<?php

namespace App\Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\BelongsToInstitution;

class Faculty extends Model
{
    use BelongsToInstitution;

    protected $guarded = ['id'];
    protected $casts = ['active' => 'boolean'];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function dean()
    {
        return $this->belongsTo(\App\Models\User::class, 'dean_id');
    }
}
