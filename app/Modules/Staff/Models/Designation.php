<?php

namespace App\Modules\Staff\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\BelongsToInstitution;

class Designation extends Model
{
    use BelongsToInstitution;

    protected $guarded = ['id'];
    protected $casts = ['active' => 'boolean'];

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }
}
