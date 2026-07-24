<?php

namespace App\Models;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PurchaseIssuance extends Model
{
    protected $fillable = [
        'purchase_id',
        'purchase_item_id',
        'employee_id',
        'quantity',
        'issued_at',
        'ris_no',
        'ics_no',
        'inventory_lifespan',
        'inventory_no',
        'inventory_details',
        'rspi_serial_no',
        'rsmi_serial_no',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'inventory_lifespan' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $issuance): void {
            if (blank($issuance->ris_no)) {
                $issuance->ris_no = self::generateRisNumber($issuance->purchase_id, $issuance->employee_id, $issuance->issued_at);
            }

            if (filled($issuance->inventory_lifespan) && blank($issuance->ics_no)) {
                $issuance->ics_no = self::generateIcsNumber($issuance->purchase_id, $issuance->employee_id, $issuance->issued_at);
            }

            $isInventoryItem = optional(PurchaseItem::with('item')->find($issuance->purchase_item_id)?->item)->inventory_item ?? false;

            if ($isInventoryItem && blank($issuance->inventory_no)) {
                $issuance->inventory_no = self::generateInventoryNumber($issuance->purchase_id, $issuance->purchase_item_id, $issuance->issued_at);
            }

            if (self::isInventoryPurchase($issuance->purchase_id) && blank($issuance->rspi_serial_no)) {
                $issuance->rspi_serial_no = self::generateRspiSerialNumber($issuance->purchase_id, $issuance->issued_at);
            }

            if (blank($issuance->inventory_no) && blank($issuance->rsmi_serial_no)) {
                $issuance->rsmi_serial_no = self::generateRsmiSerialNumber($issuance->purchase_id, $issuance->issued_at);
            }
        });
    }

    protected static function generateRisNumber(int $purchaseId, int $employeeId, $issuedAt): string
    {
        $purchase = Purchase::find($purchaseId);
        $issuedAt = $issuedAt ? Carbon::parse($issuedAt) : now();
        $yearMonth = $issuedAt->format('Y-m');
        $schoolId = $purchase?->school;

        $existingPurchaseEmployeeIssuance = self::query()
            ->whereHas('purchase', fn ($query) => $query->where('school', $schoolId))
            ->where('purchase_id', $purchaseId)
            ->where('employee_id', $employeeId)
            ->whereYear('issued_at', $issuedAt->year)
            ->whereNotNull('ris_no')
            ->orderBy('id')
            ->first();

        if ($existingPurchaseEmployeeIssuance) {
            return $existingPurchaseEmployeeIssuance->ris_no;
        }

        $series = self::query()
            ->whereHas('purchase', fn ($query) => $query->where('school', $schoolId))
            ->whereYear('issued_at', $issuedAt->year)
            ->whereNotNull('ris_no')
            ->distinct('employee_id')
            ->count('employee_id');

        return sprintf('%s-%03d', $yearMonth, ($series ?: 0) + 1);
    }

    protected static function generateIcsNumber(int $purchaseId, int $employeeId, $issuedAt): string
    {
        $purchase = Purchase::find($purchaseId);
        $issuedAt = $issuedAt ? Carbon::parse($issuedAt) : now();
        $yearMonth = $issuedAt->format('Y-m');
        $schoolId = $purchase?->school;

        $existingPurchaseEmployeeIssuance = self::query()
            ->whereHas('purchase', fn ($query) => $query->where('school', $schoolId))
            ->where('purchase_id', $purchaseId)
            ->where('employee_id', $employeeId)
            ->whereYear('issued_at', $issuedAt->year)
            ->whereNotNull('ics_no')
            ->orderBy('id')
            ->first();

        if ($existingPurchaseEmployeeIssuance) {
            return $existingPurchaseEmployeeIssuance->ics_no;
        }

        $series = self::query()
            ->whereHas('purchase', fn ($query) => $query->where('school', $schoolId))
            ->whereYear('issued_at', $issuedAt->year)
            ->whereNotNull('ics_no')
            ->selectRaw('count(distinct concat(purchase_id, \'-\', employee_id)) as aggregate')
            ->value('aggregate');

        return sprintf('%s-%03d', $yearMonth, ($series ?: 0) + 1);
    }

    protected static function generateInventoryNumber(int $purchaseId, int $purchaseItemId, $issuedAt): string
    {
        $purchase = Purchase::find($purchaseId);
        $issuedAt = $issuedAt ? Carbon::parse($issuedAt) : now();
        $year = $issuedAt->format('Y');
        $month = $issuedAt->format('m');
        $stockNo = optional(PurchaseItem::find($purchaseItemId)?->item)->stock_no;
        $schoolId = $purchase?->school;

        $series = self::query()
            ->whereHas('purchase', fn ($query) => $query->where('school', $schoolId))
            ->whereYear('issued_at', $year)
            ->whereNotNull('inventory_no')
            ->count();

        return sprintf('%s-%s-%s-%03d', $year, $month, $stockNo ?: '00', $series + 74);
    }

    protected static function isInventoryPurchase(int $purchaseId): bool
    {
        $purchase = Purchase::find($purchaseId);

        if (! $purchase) {
            return false;
        }

        return $purchase->purchaseItems()->with('item')->get()->contains(fn (PurchaseItem $purchaseItem) => (bool) optional($purchaseItem->item)->inventory_item);
    }

    protected static function generateRspiSerialNumber(int $purchaseId, $issuedAt): string
    {
        $purchase = Purchase::find($purchaseId);
        $issuedAt = $issuedAt ? Carbon::parse($issuedAt) : now();
        $year = $issuedAt->format('Y');
        $month = $issuedAt->format('m');
        $schoolId = $purchase?->school;

        $existingPurchaseIssuance = self::query()
            ->whereHas('purchase', fn ($query) => $query->where('school', $schoolId))
            ->where('purchase_id', $purchaseId)
            ->whereNotNull('rspi_serial_no')
            ->orderBy('id')
            ->first();

        if ($existingPurchaseIssuance) {
            return $existingPurchaseIssuance->rspi_serial_no;
        }

        $series = self::query()
            ->whereHas('purchase', fn ($query) => $query->where('school', $schoolId))
            ->whereYear('issued_at', $year)
            ->whereNotNull('rspi_serial_no')
            ->selectRaw('count(distinct purchase_id) as aggregate')
            ->value('aggregate');

        return sprintf('%s-%s-%03d', $year, $month, ($series ?: 0) + 1);
    }

    protected static function generateRsmiSerialNumber(int $purchaseId, $issuedAt): string
    {
        $purchase = Purchase::find($purchaseId);
        $issuedAt = $issuedAt ? Carbon::parse($issuedAt) : now();
        $year = $issuedAt->format('Y');
        $month = $issuedAt->format('m');
        $schoolId = $purchase?->school;

        $existingPurchaseIssuance = self::query()
            ->whereHas('purchase', fn ($query) => $query->where('school', $schoolId))
            ->where('purchase_id', $purchaseId)
            ->whereNotNull('rsmi_serial_no')
            ->orderBy('id')
            ->first();

        if ($existingPurchaseIssuance) {
            return $existingPurchaseIssuance->rsmi_serial_no;
        }

        $series = self::query()
            ->whereHas('purchase', fn ($query) => $query->where('school', $schoolId))
            ->whereYear('issued_at', $year)
            ->whereNotNull('rsmi_serial_no')
            ->selectRaw('count(distinct purchase_id) as aggregate')
            ->value('aggregate');

        return sprintf('%s-%s-%03d', $year, $month, ($series ?: 0) + 1);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function purchaseItem()
    {
        return $this->belongsTo(PurchaseItem::class, 'purchase_item_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
