<?php

namespace App\Models;

use App\Models\BACMember;
use App\Models\Employee;
use App\Models\PurchaseInspection;
use App\Models\PurchaseItem;
use App\Models\PurchaseItemSupplier;
use App\Models\PurchaseIssuance;
use App\Models\PurchaseOrder;
use App\Models\PurchasePicture;
use App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Schema;

class Purchase extends Model
{
    protected static function booted(): void
    {
        static::creating(function (self $purchase): void {
            if (blank($purchase->approved_by) && filled($purchase->school)) {
                $senior = BACMember::query()
                    ->where('school_id', $purchase->school)
                    ->where('role', BACMember::ROLE_SENIOR_BOOKKEEPER)
                    ->with('employee')
                    ->first();

                if ($senior?->employee?->id) {
                    $purchase->approved_by = $senior->employee->id;
                    $purchase->senior_bookkeeper_id = $senior->employee->id;
                    $purchase->senior_bookkeeper_name = $senior->employee->full_name;
                }
            }
        });

        static::saved(function (self $purchase): void {
            if (! filled($purchase->po_date)) {
                return;
            }

            if (! Schema::hasTable('purchase_orders')) {
                return;
            }

            $purchaseOrder = $purchase->purchaseOrder()->first();

            if ($purchaseOrder) {
                $purchaseOrder->fill([
                    'school_id' => $purchase->school,
                    'po_date' => $purchase->po_date,
                ])->save();

                return;
            }

            $purchase->purchaseOrder()->create([
                'school_id' => $purchase->school,
                'po_date' => $purchase->po_date,
            ]);
        });
    }

    protected $fillable = [
        'school',
        'purpose',
        'fund_cluster',
        'rc_code',
        'pr_no',
        'pr_date',
        'po_date',
        'dv_date',
        'inspection_date',
        'requested_by',
        'approved_by',
        'senior_bookkeeper_id',
        'senior_bookkeeper_name',
    ];

    public function requester(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        // Change 'requested_by' if your foreign key column is named differently
        return $this->belongsTo(Employee::class, 'requested_by'); 
    }

    public function approver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        // Change 'requested_by' if your foreign key column is named differently
        return $this->belongsTo(Employee::class, 'approved_by'); 
    }

    public function seniorBookkeeper(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class, 'senior_bookkeeper_id');
    }

    public function getSeniorBookkeeperNameAttribute(): ?string
    {
        return $this->seniorBookkeeper?->full_name ?? $this->attributes['senior_bookkeeper_name'] ?? null;
    }

    public function schoolRelation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(School::class, 'school');
    }

    public function purchaseItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id');
    }

    public function purchaseOrder(): HasOne
    {
        return $this->hasOne(PurchaseOrder::class, 'purchase_id');
    }

    public function inspection(): HasOne
    {
        return $this->hasOne(PurchaseInspection::class, 'purchase_id');
    }

    public function issuances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PurchaseIssuance::class, 'purchase_id');
    }

    public function supplierQuotes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PurchaseItemSupplier::class, 'purchase_id');
    }

    public function pictures(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PurchasePicture::class, 'purchase_id');
    }
}
