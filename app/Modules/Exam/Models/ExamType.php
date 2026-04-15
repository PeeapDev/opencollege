<?php

namespace App\Modules\Exam\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\BelongsToInstitution;

class ExamType extends Model
{
    use BelongsToInstitution;

    protected $guarded = ['id'];
    protected $casts = ['active' => 'boolean', 'weight_percentage' => 'decimal:2'];

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}
