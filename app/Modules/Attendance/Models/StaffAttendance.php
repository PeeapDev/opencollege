<?php

namespace App\Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\BelongsToInstitution;

class StaffAttendance extends Model
{
    use BelongsToInstitution;

    protected $guarded = ['id'];
    protected $casts = ['date' => 'date'];

    public function staff()
    {
        return $this->belongsTo(\App\Modules\Staff\Models\Staff::class);
    }
}
