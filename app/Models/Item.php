<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PurchaseItem;
use App\Models\School;
use App\Models\UacsCode;

class Item extends Model
{
    protected $fillable = [
        'school_id',
        'stock_no',
        'name',
        'unit',
        'item_unit_price',
        'inventory_item',
    ];

    protected static function booted(): void
    {
        static::creating(function (Item $item): void {
            if ($item->school_id && $item->stock_no === null) {
                $maxStockNo = Item::where('school_id', $item->school_id)->max('stock_no');
                $nextStockNo = (int) $maxStockNo + 1;
                $item->stock_no = str_pad((string) $nextStockNo, 3, '0', STR_PAD_LEFT);
            }

            if ($item->stock_no !== null && $item->stock_no !== '') {
                $item->stock_no = str_pad((string) $item->stock_no, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function uacsCode(): BelongsTo
    {
        return $this->belongsTo(UacsCode::class, 'uacs_code_id');
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
