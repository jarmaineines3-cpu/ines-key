<?php

namespace App\Filament\Resources\BACMembers\Schemas;

use App\Models\BACMember;
use App\Models\Employee;
use App\Models\School;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class BACMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->label('School')
                    ->options(School::query()->orderBy('school_name', 'asc')->pluck('school_name', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive(),
                Select::make('employee_id')
                    ->label('Employee')
                    ->searchable()
                    ->required()
                    ->options(function (callable $get) {
                        $schoolId = $get('school_id');

                        return Employee::query()
                            ->when($schoolId, fn ($query) => $query->whereHas('schools', fn ($query) => $query->where('employee_school.school_id', $schoolId)))
                            ->orderBy('full_name', 'asc')
                            ->pluck('full_name', 'id');
                    }),
                Select::make('role')
                    ->label('BAC Role')
                    ->options(BACMember::getRoleOptions())
                    ->required()
                    ->reactive()
                    ->rules([
                        function ($attribute, $value, $fail) {
                            if (! $value || $value === BACMember::ROLE_MEMBER) {
                                return;
                            }

                            $schoolId = request()->input('school_id') ?? null;
                            $recordId = request()->route('record');

                            if (! $schoolId) {
                                return;
                            }

                            $exists = BACMember::query()
                                ->where('school_id', $schoolId)
                                ->where('role', $value)
                                ->when($recordId, fn ($query, $recordId) => $query->where('id', '!=', $recordId))
                                ->exists();

                            if ($exists) {
                                $fail('There is already a BAC member with this role for the selected school.');
                            }
                        },
                    ]),
            ]);
    }
}
