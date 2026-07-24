<?php

namespace App\Models;

use App\Models\BACMember;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    protected $fillable = [
        'school_id',
        'school_name',
        'school_head',
        'school_address',
        'school_contact',
        'school_email',
        'school_social',
        'school_logo',
    ];
    // Add this method to your School model
    public function employees()
    {
        return $this->belongsToMany(Employee::class)
                ->withPivot(['id', 'assigned_at']) // 👈 Include 'id' here
                ->withTimestamps();
    }

    // Make sure the relationship name matches what you use in the table
    public function head(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'school_head');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'school_id');
    }

    public function bacMembers()
    {
        return $this->hasMany(BACMember::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'school_id');
    }
}
