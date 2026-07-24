<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInspection extends Model
{
    protected $fillable = [
        'purchase_id',
        'inspector_id',
        'inspection_date',
        'invoice_no',
        'invoice_date',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'invoice_date' => 'date',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function inspector()
    {
        return $this->belongsTo(Employee::class, 'inspector_id');
    }
}
