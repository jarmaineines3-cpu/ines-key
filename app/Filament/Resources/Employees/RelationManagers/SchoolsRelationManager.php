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
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchoolsRelationManager extends RelationManager
{
    protected static string $relationship = 'schools';

    // Add this method to disable Read-Only mode on the View page
    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('school_id')
                    ->required(),
                TextInput::make('school_name')
                    ->required(),
                TextInput::make('school_address')
                    ->required(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('school_id'),
                TextEntry::make('school_name'),
                TextEntry::make('school_address'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->allowDuplicates()
            ->recordTitleAttribute('school_name')
            ->columns([
                TextColumn::make('school_name')
                    ->searchable(),
                TextColumn::make('assigned_at')
                    ->date('F d, Y')
                    ->label('Date Assigned')
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
                    ->modalHeading('Select School')
                    ->modalSubmitActionLabel('OK')
                    ->attachAnother(false)
                    ->label('Assign School')
                    ->form(fn (AttachAction $action) => [
                        $action->getRecordSelect(), // Brings back the school dropdown
                        DatePicker::make('assigned_at')
                            ->default(now())
                            ->required(),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->form([
                        DatePicker::make('assigned_at')
                            ->label('Assignment Date')
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
