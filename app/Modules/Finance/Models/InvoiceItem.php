<?php

namespace App\Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['amount' => 'decimal:2'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function feeCategory()
    {
        return $this->belongsTo(FeeCategory::class);
    }
}
