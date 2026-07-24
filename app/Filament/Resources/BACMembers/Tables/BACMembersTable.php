<?php

namespace App\Filament\Resources\BACMembers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class BACMembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('role')
            ->columns([
                TextColumn::make('school.school_name')
                    ->label('School')
                    ->searchable(),
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('BAC Role')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('chairperson')
                    ->query(fn ($query) => $query->where('role', 'chairperson'))
                    ->label('Chairperson'),
                Filter::make('vice_chairperson')
                    ->query(fn ($query) => $query->where('role', 'vice chairperson'))
                    ->label('Vice Chairperson'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
