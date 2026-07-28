<?php

namespace App\Filament\Resources\Employees\Schemas;

use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EmployeeInfolist
{
    public static function getLeaveSummary($record): ?string
    {
        $leaveTypes = ['vacation_leave', 'sick', 'wellness', 'forced', 'terminal', 'special_privilege', 'maternity', 'solo_parent', 'study', 'vawc', 'rehabilitation_privilege', 'special_emergency_calamity_leave', 'adoption', 'leave'];
        $rows = [];

        foreach ($leaveTypes as $leaveType) {
            if ($leaveType === 'wellness') {
                continue;
            }

            $credits = (float) $record->leaves()
                ->where('leave_type', $leaveType)
                ->sum('leave_credits');

            if ($credits > 0) {
                $rows[] = [
                    'label' => ucfirst(str_replace('_', ' ', $leaveType)),
                    'credits' => self::formatLeaveCredits($credits),
                ];
            }
        }

        $wellnessCredits = (float) $record->leaves()
            ->where('leave_type', 'wellness')
            ->sum('leave_credits');

        if ($wellnessCredits > 0) {
            $rows[] = [
                'label' => 'Wellness',
                'credits' => self::formatLeaveCredits($wellnessCredits),
            ];
        } else {
            $rows[] = [
                'label' => 'Wellness',
                'credits' => self::formatLeaveCredits(5),
            ];
        }

        if (empty($rows)) {
            return null;
        }

        $tableRows = implode('', array_map(function ($row) {
            return '<tr><td>' . e($row['label']) . '</td><td style="text-align:right;">' . e($row['credits']) . '</td></tr>';
        }, $rows));

        return '<table style="width:100%; border-collapse:collapse;">' .
            '<thead><tr><th style="text-align:left; padding:2px 4px;">Leave Type</th><th style="text-align:right; padding:2px 4px;">Credits</th></tr></thead>' .
            '<tbody>' . $tableRows . '</tbody></table>';
    }

    private static function formatLeaveCredits(float $credits): string
    {
        return number_format($credits, 2, '.', '');
    }

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
                TextEntry::make('leave_summary')
                    ->label('Leave Credits')
                    ->placeholder('No leave credits recorded.')
                    ->state(function ($record) {
                        return self::getLeaveSummary($record);
                    })
                    ->html(),

            ]);
    }
}
