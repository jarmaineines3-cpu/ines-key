<?php

namespace App\Filament\Resources\Purchases\Schemas;

use App\Models\BACMember;
use App\Models\Employee;
use App\Models\Purchase;
use App\Models\School;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PurchaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school')
                    ->required()
                    ->options(School::query()->pluck('school_name', 'id'))
                    ->live() // Makes the field reactive
                    ->afterStateUpdated(function ($state, Set $set) { // 2. Use $state instead of $get
                        $schoolId = $state; 
                        if (!$schoolId) return;

                        $year = Carbon::now()->format('Y');
                        $month = Carbon::now()->format('m');
                        
                        // Count how many purchases this school has made this year
                        $count = Purchase::query()
                            ->where('school', '=', $schoolId, 'and')
                            ->whereYear('pr_date', '=', $year, 'and')
                            ->count('*');

                        // Increment for the new PR number (e.g., 2026-09-001)
                        $newNumber = $count + 1;
                        $prNo = sprintf('%s-%s-%03d', $year, $month, $newNumber);

                        $set('pr_no', $prNo);

                        $custodian = BACMember::query()
                            ->where('school_id', $schoolId)
                            ->where('role', BACMember::ROLE_PROPERTY_CUSTODIAN)
                            ->first();

                        if ($custodian) {
                            $set('requested_by', $custodian->employee_id);
                        }

                        $hope = BACMember::query()
                            ->where('school_id', $schoolId)
                            ->where('role', BACMember::ROLE_HOPE)
                            ->first();

                        if ($hope) {
                            $set('approved_by', $hope->employee_id);
                        }
                    }),
                TextInput::make('purpose')
                    ->required(),
                TextInput::make('fund_cluster')
                    ->required()
                    ->default('1101101'),
                TextInput::make('rc_code')
                    ->required()
                    ->default('08-08-003'),
                TextInput::make('pr_no')
                    ->required()
                    ->unique(ignoreRecord: true),
                DatePicker::make('pr_date')
                    ->required()
                    ->default(now()),
                DatePicker::make('dv_date')
                    ->hiddenOn('create')
                    ->default(now()),
                Select::make('requested_by')
                    ->required()
                    ->options(
                        Employee::query()
                           ->pluck('full_name', 'id') // 1. Title to display, 2. Value saved in database
                    ),
                Select::make('approved_by')
                    ->required()
                    ->options(
                        Employee::query()
                           ->pluck('full_name', 'id') // 1. Title to display, 2. Value saved in database
                    ),
                Select::make('senior_bookkeeper_id')
                    ->required()
                    ->options(
                        Employee::query()
                           ->pluck('full_name', 'id') // 1. Title to display, 2. Value saved in database
                    ),
            ]);
    }
}
