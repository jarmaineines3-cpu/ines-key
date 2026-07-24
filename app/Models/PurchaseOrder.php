<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';

    protected $fillable = [
        'purchase_id',
        'school_id',
        'po_no',
        'po_date',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $purchaseOrder): void {
            $purchaseOrder->school_id ??= $purchaseOrder->purchase?->school;
            $purchaseOrder->po_date ??= $purchaseOrder->purchase?->po_date;

            if (! filled($purchaseOrder->po_no) && filled($purchaseOrder->school_id) && filled($purchaseOrder->po_date)) {
                $poDate = Carbon::parse($purchaseOrder->po_date);
                $year = $poDate->year;

                $startOfYear = Carbon::create($year, 1, 1, 0, 0, 0);
                $startOfNextYear = Carbon::create($year + 1, 1, 1, 0, 0, 0);

                $count = self::query()
                    ->where('school_id', $purchaseOrder->school_id)
                    ->where('po_date', '>=', $startOfYear)
                    ->where('po_date', '<', $startOfNextYear)
                    ->when($purchaseOrder->id, fn ($query) => $query->where('id', '!=', $purchaseOrder->id))
                    ->count();

                $purchaseOrder->po_no = sprintf('%s-%02d-%03d', $year, $poDate->month, $count + 1);
            }
        });
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
