<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class BACMember extends Model
{
    use HasFactory;

    protected $table = 'bac_members';

    protected $fillable = [
        'school_id',
        'employee_id',
        'role',
    ];

    public const ROLE_CHAIRPERSON = 'chairperson';
    public const ROLE_VICE_CHAIRPERSON = 'vice chairperson';
    public const ROLE_MEMBER = 'member';
    public const ROLE_HOPE = 'bac hope';
    public const ROLE_PROPERTY_CUSTODIAN = 'property custodian';
    public const ROLE_INSPECTOR = 'inspector';
    public const ROLE_SENIOR_BOOKKEEPER = 'senior bookkeeper';

    public static function getRoleOptions(): array
    {
        return [
            self::ROLE_CHAIRPERSON => 'BAC Chairperson',
            self::ROLE_VICE_CHAIRPERSON => 'BAC Vice Chairperson',
            self::ROLE_MEMBER => 'BAC Member',
            self::ROLE_HOPE => 'BAC HOPE',
            self::ROLE_PROPERTY_CUSTODIAN => 'Property Custodian',
            self::ROLE_INSPECTOR => 'Inspector',
            self::ROLE_SENIOR_BOOKKEEPER => 'Senior Bookkeeper',
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    protected static function booted(): void
    {
        static::saving(function (BACMember $member) {
            if ($member->role === self::ROLE_MEMBER) {
                return;
            }

            $exists = self::query()
                ->where('school_id', $member->school_id)
                ->where('role', $member->role)
                ->when($member->exists, fn ($query) => $query->where('id', '!=', $member->id))
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'role' => 'There is already a BAC member with this role for this school.',
                ]);
            }
        });
    }
}
