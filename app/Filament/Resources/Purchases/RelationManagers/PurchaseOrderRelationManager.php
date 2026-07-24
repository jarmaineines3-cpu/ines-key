<?php

namespace App\Filament\Resources\Purchases\RelationManagers;

use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchaseOrderRelationManager extends RelationManager
{
    protected static string $relationship = 'purchaseOrder';

    protected function nextPoNumber(): string
    {
        $purchase = $this->getOwnerRecord();
        $schoolId = $purchase?->school;
        $today = Carbon::now();
        $year = $today->year;
        $month = $today->month;

        $count = PurchaseOrder::query()
            ->where('school_id', $schoolId)
            ->where('po_date', '>=', Carbon::create($year, 1, 1, 0, 0, 0))
            ->where('po_date', '<', Carbon::create($year + 1, 1, 1, 0, 0, 0))
            ->count('*');

        return sprintf('%s-%02d-%03d', $year, $month, $count + 1);
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('po_no')
                    ->label('PO No.')
                    ->required(),
                DatePicker::make('po_date')
                    ->label('PO Date')
                    ->default(now())
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('po_no')
            ->columns([
                TextColumn::make('po_no')
                    ->label('PO No.')
                    ->searchable(),
                TextColumn::make('po_date')
                    ->label('PO Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('school.school_name')
                    ->label('School')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make('createPurchaseOrder')
                    ->label('Add PO')
                    ->modalHeading('Create Purchase Order')
                    ->modalSubmitActionLabel('Save PO')
                    ->fillForm(fn (): array => [
                        'po_no' => $this->nextPoNumber(),
                        'po_date' => now()->toDateString(),
                    ])
                    ->action(function (array $data): void {
                        $this->getOwnerRecord()?->purchaseOrder()->create($data);
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->filters([]);
    }
}
