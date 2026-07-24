<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Item;
use App\Models\Purchase;

class PurchaseItem extends Model
{
    protected $table = 'purchase_item';

    protected $fillable = [
        'purchase_id',
        'item_id',
        'item_name',
        'item_unit',
        'item_quantity',
        'item_unit_price',
    ];

    protected static function booted(): void
    {
        static::saving(function (PurchaseItem $purchaseItem): void {
            if ($purchaseItem->item_id) {
                $item = Item::find($purchaseItem->item_id);

                if (! $item) {
                    return;
                }

                $purchaseItem->item_name = $item->name;
                $purchaseItem->item_unit = $item->unit;

                if ($purchaseItem->item_unit_price === null) {
                    $purchaseItem->item_unit_price = $item->item_unit_price;
                }
            }
        });
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
