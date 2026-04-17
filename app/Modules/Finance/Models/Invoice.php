<?php

namespace App\Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\BelongsToInstitution;
use App\Traits\LogsAudit;

class Invoice extends Model
{
    use BelongsToInstitution;
    use LogsAudit;

    protected $guarded = ['id'];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(\App\Modules\Student\Models\Student::class);
    }

    public function semester()
    {
        return $this->belongsTo(\App\Modules\Academic\Models\Semester::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
