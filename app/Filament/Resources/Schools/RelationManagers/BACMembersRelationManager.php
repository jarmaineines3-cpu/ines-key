<?php

namespace App\Filament\Resources\Schools\RelationManagers;

use App\Filament\Resources\Schools\SchoolResource;
use App\Models\BACMember;
use App\Models\Employee;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class BACMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'bacMembers';

    public function isReadOnly(): bool
    {
        return false;
    }

    protected static ?string $recordTitleAttribute = 'role';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                    ->label('Employee')
                    ->searchable()
                    ->required()
                    ->options(function () {
                        $schoolId = $this->getOwnerRecord()->id;

                        return Employee::query()
                            ->orderBy('full_name')
                            ->pluck('full_name', 'id');
                    }),
                Select::make('role')
                    ->label('BAC Role')
                    ->options(BACMember::getRoleOptions())
                    ->required()
                    ->rules(['required']),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('role')
            ->columns([
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
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
