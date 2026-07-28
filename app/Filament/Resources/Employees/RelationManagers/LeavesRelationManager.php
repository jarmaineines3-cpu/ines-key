<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeavesRelationManager extends RelationManager
{
    protected static string $relationship = 'leaves';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('leave_type')
                    ->label('Leave Type')
                    ->options([
                        'vacation_leave' => 'Vacation Leave',
                        'sick' => 'Sick',
                        'wellness' => 'Wellness',
                        // 'forced' => 'Forced',
                        // 'terminal' => 'Terminal',
                        // 'special_privilege' => 'Special Privilege',
                        // 'maternity' => 'Maternity',
                        // 'solo_parent' => 'Solo Parent',
                        // 'study' => 'Study',
                        // 'vawc' => 'VAWC',
                        // 'rehabilitation_privilege' => 'Rehabilitation Privilege',
                        // 'special_leave_benefit_for_women' => 'Special Leave Benefit for Women',
                        // 'special_emergency_calamity_leave' => 'Special Emergency Calamity Leave',
                        // 'adoption' => 'Adoption',
                        // 'leave' => 'Leave',
                    ])
                    ->required(),
                TextInput::make('leave_credits')
                    ->label('Leave Credits')
                    ->numeric()
                    ->required(),
                DatePicker::make('as_of_date')
                    ->label('As Of Date')
                    ->required(),
                Textarea::make('details')
                    ->label('Details')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('leave_type')
            ->columns([
                TextColumn::make('leave_type')
                    ->label('Leave Type')
                    ->searchable(),
                TextColumn::make('leave_credits')
                    ->label('Leave Credits')
                    ->sortable(),
                TextColumn::make('as_of_date')
                    ->label('As Of Date')
                    ->date('F d, Y')
                    ->sortable(),
                TextColumn::make('details')
                    ->wrap(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                Action::make('requestLeave')
                    ->label('Request Leave')
                    ->form([
                        Select::make('leave_type')
                            ->label('Leave Type')
                            ->options([
                                'vacation_leave' => 'Vacation Leave',
                                'sick' => 'Sick',
                                'wellness' => 'Wellness',
                                // 'forced' => 'Forced',
                                // 'terminal' => 'Terminal',
                                // 'special_privilege' => 'Special Privilege',
                                // 'maternity' => 'Maternity',
                                // 'solo_parent' => 'Solo Parent',
                                // 'study' => 'Study',
                                // 'vawc' => 'VAWC',
                                // 'rehabilitation_privilege' => 'Rehabilitation Privilege',
                                // 'special_leave_benefit_for_women' => 'Special Leave Benefit for Women',
                                // 'special_emergency_calamity_leave' => 'Special Emergency Calamity Leave',
                                // 'adoption' => 'Adoption',
                                // 'leave' => 'Leave',
                            ])
                            ->required(),
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->required(),
                        Textarea::make('details')
                            ->label('Details')
                            ->rows(3),
                    ])
                    ->action(function (array $data) {
                        $start = \Carbon\Carbon::parse($data['start_date']);
                        $end = \Carbon\Carbon::parse($data['end_date']);

                        $workingDays = 0;
                        $current = $start->copy();

                        while ($current->lte($end)) {
                            if (! in_array($current->dayOfWeek, [0, 6])) {
                                $workingDays++;
                            }
                            $current->addDay();
                        }

                        $this->getRelationship()->create([
                            'employee_id' => $this->getOwnerRecord()->id,
                            'leave_type' => $data['leave_type'],
                            'leave_credits' => -$workingDays,
                            'as_of_date' => $end->toDateString(),
                            'details' => $data['details'] ?? null,
                        ]);
                    }),
                CreateAction::make()
                    ->label('Add Leave Credits'),
                DeleteBulkAction::make(),
            ]);
    }
}
