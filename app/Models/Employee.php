<?php

namespace App\Models;

use App\Models\BACMember;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Employee extends Model
{
    protected $fillable = ['fname', 'mname', 'lname', 'xname', 'full_name','birthday'];

    // Many-to-many relationship tracking history
    public function schools()
    {
        return $this->belongsToMany(School::class)
                ->withPivot(['id', 'assigned_at']) // 👈 Include 'id' here
                ->withTimestamps()
                ->orderByPivot('assigned_at', 'desc');
    }

    // Dynamic accessor to get the single current school
    public function getCurrentSchoolAttribute()
    {
        return $this->schools()->first(); 
    }

    // Many-to-many relationship tracking history
    public function positions()
    {
        return $this->belongsToMany(Position::class)
                ->withPivot(['id', 'appointment_date']) // 👈 Include 'id' here
                ->withTimestamps()
                ->orderByPivot('appointment_date', 'desc');
    }

    // Dynamic accessor to get the single current position
    public function getCurrentPositionAttribute()
    {
        return $this->positions()->first(); 
    }

    // Dynamic accessor to get the single original position
    public function getoriginalPositionAttribute()
    {
        return $this->belongsToMany(Position::class)
                ->withPivot(['id', 'appointment_date']) // 👈 Include 'id' here
                ->withTimestamps()
                ->orderByPivot('appointment_date', 'asc')->first();
    }

    public function bacMembers()
    {
        return $this->hasMany(BACMember::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }
    
    public function getSalaryStepAttribute(): int
    {
        $currentPosition = $this->current_position;

        // If no position is assigned yet, default to Step 1
        if (! $currentPosition || ! $currentPosition->pivot->appointment_date) {
            return 1;
        }

        $appointmentDate = Carbon::parse($currentPosition->pivot->appointment_date);
        
        // Calculate the difference in full years between the appointment date and today
        $yearsInPosition = $appointmentDate->diffInYears(now());

        // Formula: Step 1 (Base) + 1 Step for every 3 full years of service
        $calculatedStep = 1 + floor($yearsInPosition / 3);

        // Optional: Cap it at Step 8 (Standard max step for most SG positions)
        return min($calculatedStep, 8);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($employee) {
            // Capitalize each name component individually
            $employee->fname = Str::title(trim($employee->fname));
            $employee->mname = Str::title(trim($employee->mname));
            $employee->lname = Str::title(trim($employee->lname));
            $employee->xname = Str::title(trim($employee->xname));

            // Construct capitalized full name with optional middle initial.
            $middleInitial = null;
            if ($employee->mname !== null && $employee->mname !== '') {
                $middleInitial = Str::substr($employee->mname, 0, 1) . '.';
            }

            $employee->full_name = trim(implode(' ', array_filter([
                $employee->fname,
                $middleInitial,
                $employee->lname,
                $employee->xname,
            ])));
            
            $employee->age = Carbon::parse($employee->birthday)->age;
        });

        
    }
}
