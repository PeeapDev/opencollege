<?php

namespace App\Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\BelongsToInstitution;

class FeeCategory extends Model
{
    use BelongsToInstitution;

    protected $guarded = ['id'];
    protected $casts = ['active' => 'boolean'];
}
