<?php

namespace App\Filament\Resources\Purchases\RelationManagers;

use App\Models\BACMember;
use App\Models\Employee;
use App\Models\PurchaseInspection;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InspectionRelationManager extends RelationManager
{
    protected static string $relationship = 'inspection';

    public function isReadOnly(): bool
    {
        return false;
    }

    protected function getInspectorOptions(): array
    {
        $schoolId = $this->getOwnerRecord()?->school;

        if (! $schoolId) {
            return [];
        }

        return Employee::query()
            ->whereIn('id', function ($query) use ($schoolId) {
                $query->select('employee_id')
                    ->from('bac_members')
                    ->where('school_id', $schoolId)
                    ->where('role', BACMember::ROLE_INSPECTOR);
            })
            ->orderBy('full_name')
            ->pluck('full_name', 'id')
            ->toArray();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inspector_id')
                    ->label('Inspection Officer')
                    ->options(fn (): array => $this->getInspectorOptions())
                    ->default(fn () => $this->getInspectorOptions() ? array_key_first($this->getInspectorOptions()) : null)
                    ->required(),
                DatePicker::make('inspection_date')
                    ->label('Date of Inspection')
                    ->required(),
                TextInput::make('invoice_no')
                    ->label('Invoice No.'),
                DatePicker::make('invoice_date')
                    ->label('Invoice Date'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('inspection_date')
            ->columns([
                TextColumn::make('inspector.full_name')
                    ->label('Inspection Officer')
                    ->searchable(),
                TextColumn::make('inspection_date')
                    ->label('Inspection Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('invoice_no')
                    ->label('Invoice No.'),
                TextColumn::make('invoice_date')
                    ->label('Invoice Date')
                    ->date()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make('addInspection')
                    ->label('Add Inspection')
                    ->modalHeading('Add Inspection')
                    ->modalSubmitActionLabel('Save Inspection'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
