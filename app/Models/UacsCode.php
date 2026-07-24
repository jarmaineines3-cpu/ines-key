<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UacsCode extends Model
{
    protected $fillable = [
        'uacs_code',
        'account_title',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'uacs_code_id');
    }
}
