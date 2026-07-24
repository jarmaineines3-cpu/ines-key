<?php

namespace App\Filament\Resources\Purchases\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort("pr_no", "desc")
            ->columns([
                TextColumn::make('purpose')
                    ->searchable(),
                TextColumn::make('pr_no')
                    ->searchable(),
                TextColumn::make('pr_date')
                    ->searchable(),
                TextColumn::make('requester.full_name') // Use relationship name + dot + column name
                    ->label('Requested By')        // Explicitly set the column header text
                    ->searchable(),
                TextColumn::make('approver.full_name') // Use relationship name + dot + column name
                    ->label('Approved By')        // Explicitly set the column header text
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
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
