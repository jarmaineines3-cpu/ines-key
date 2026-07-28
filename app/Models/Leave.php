<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [
        'employee_id',
        'leave_type',
        'leave_credits',
        'as_of_date',
        'start_date',
        'end_date',
        'details',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
