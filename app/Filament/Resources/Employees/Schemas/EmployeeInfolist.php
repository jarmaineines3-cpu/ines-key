<?php

namespace App\Filament\Resources\Employees\Schemas;

use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EmployeeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('employee_no'),
                TextEntry::make('sex'),
                TextEntry::make('age'),
                TextEntry::make('birthday')
                    ->date('F d, Y'),
                TextEntry::make('address'),
                TextEntry::make('current_school')
                    ->label('Current School')
                    ->placeholder('No school assigned')
                    ->state(function ($record) {
                        // Accesses your getCurrentSchoolAttribute() model accessor
                        $school = $record->current_school; 
                        
                        // Return the school name property if it exists
                        return $school ? $school->school_name : null;
                    }),
                TextEntry::make('current_position')
                    ->label('Current Position details')
                    ->placeholder('No Position Assigned')
                    ->state(function ($record) {
                        $position = $record->current_position; 

                        if (! $position) {
                            return null;
                        }

                        // Adjust column names ('name' and 'salary_grade') to match your Position table schema
                        $positionName = $position->position_name ?? 'No Position';
                        $salaryGrade = $position->salary_grade ?? 'N/A';
                        $salaryStep = $record->salary_step;

                        // Outputs format like: "Teacher I - SG 11 (Step 2)"
                        return "{$positionName} - SG {$salaryGrade} (Step {$salaryStep})";
                    }),
                TextEntry::make('original_appointment')
                    ->label('Years in service')
                    ->placeholder('N/A')
                    ->state(function ($record) {
                        // Accesses your getCurrentSchoolAttribute() model accessor
                        $position = $record->original_position; 
                        
                        $startDate = Carbon::parse($position->pivot->appointment_date ?? null);
                        $endDate = Carbon::now();


                        $diff = $startDate->diff($endDate);

                        $output = [];
                        // 🌟 Str::plural('word', count) returns 'year' for 1, and 'years' for other numbers
                        if ($diff->y > 0) {
                            $output[] = $diff->y . ' ' . Str::plural('year', $diff->y);
                        }
                        if ($diff->m > 0) {
                            $output[] = $diff->m . ' ' . Str::plural('month', $diff->m);
                        }
                        if ($diff->d > 0) {
                            $output[] = $diff->d . ' ' . Str::plural('day', $diff->d);
                        }

                        return implode(', ', $output);

                        
                    }),

            ]);
    }
}
