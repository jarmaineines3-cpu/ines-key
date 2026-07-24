<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PositionsRelationManager extends RelationManager
{
    protected static string $relationship = 'positions';

    // Add this method to disable Read-Only mode on the View page
    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('position_name')
                    ->required(),
                TextInput::make('salary_grade')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('position_name')
            ->columns([
                TextColumn::make('position_name')
                    ->searchable(),
                TextColumn::make('salary_grade')
                    ->searchable(),
                TextColumn::make('appointment_date')
                    ->date('F d, Y')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //CreateAction::make(),
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->modalHeading('Select Position')
                    ->modalSubmitActionLabel('OK')
                    ->attachAnother(false)
                    ->label('Assign Position')
                    ->form(fn (AttachAction $action) => [
                        $action->getRecordSelect(), // Brings back the school dropdown
                        DatePicker::make('appointment_date')
                            ->default(now())
                            ->required(),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        DatePicker::make('appointment_date')
                            ->label('Appointment Date')
                            ->required(),
                    ]),
                DetachAction::make()
                    ->label('Remove'),
                
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
