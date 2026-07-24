<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    public function employees()
    {
        return $this->belongsToMany(Employee::class)
                ->withPivot(['id', 'appointment_date']) // 👈 Include 'id' here
                ->withTimestamps();
    }
}
