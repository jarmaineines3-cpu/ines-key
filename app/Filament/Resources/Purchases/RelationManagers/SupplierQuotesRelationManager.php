<?php

namespace App\Filament\Resources\Purchases\RelationManagers;

use App\Filament\Resources\Purchases\PurchaseResource;
use App\Models\PurchaseItem;
use App\Models\PurchaseItemSupplier;
use App\Models\Supplier;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SupplierQuotesRelationManager extends RelationManager
{
    protected static string $relationship = 'supplierQuotes';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('supplier_id')
                    ->label('Supplier')
                    ->searchable()
                    ->options(fn (): array => Supplier::query()
                        ->orderBy('supplier_name')
                        ->pluck('supplier_name', 'id')
                        ->toArray())
                    ->required(),
                Select::make('purchase_item_id')
                    ->label('Purchase Item')
                    ->searchable()
                    ->options(fn (): array => PurchaseItem::query()
                        ->where('purchase_id', $this->getOwnerRecord()?->id)
                        ->orderBy('item_name')
                        ->pluck('item_name', 'id')
                        ->toArray())
                    ->required(),
                TextInput::make('item_unit_price')
                    ->label('Supplier Unit Price')
                    ->numeric()
                    ->required(),
            ]);
    }

    protected function getSupplierOptions(): array
    {
        return Supplier::query()
            ->orderBy('supplier_name')
            ->pluck('supplier_name', 'id')
            ->toArray();
    }

    protected function getAvailableSupplierOptions(): array
    {
        $existingSupplierIds = $this->getOwnerRecord()?->supplierQuotes()->pluck('supplier_id')->toArray() ?? [];

        return Supplier::query()
            ->whereNotIn('id', $existingSupplierIds)
            ->orderBy('supplier_name')
            ->pluck('supplier_name', 'id')
            ->toArray();
    }

    protected function buildQuoteItemsForSupplier(?int $supplierId): array
    {
        $purchaseItems = PurchaseItem::query()
            ->where('purchase_id', $this->getOwnerRecord()?->id)
            ->get();

        $existingQuotes = PurchaseItemSupplier::query()
            ->where('purchase_id', $this->getOwnerRecord()?->id)
            ->when($supplierId, fn ($query) => $query->where('supplier_id', $supplierId))
            ->get()
            ->keyBy('purchase_item_id');

        return $purchaseItems->map(function (PurchaseItem $purchaseItem) use ($existingQuotes) {
            return [
                'purchase_item_id' => $purchaseItem->id,
                'item_name' => $purchaseItem->item_name,
                'item_quantity' => $purchaseItem->item_quantity,
                'item_unit_price' => $existingQuotes[$purchaseItem->id]->item_unit_price ?? null,
            ];
        })->toArray();
    }

    protected function saveSupplierQuoteData(array $data): void
    {
        $purchase = $this->getOwnerRecord();

        if (! $purchase || empty($data['supplier_id']) || empty($data['quote_items'])) {
            return;
        }

        foreach ($data['quote_items'] as $quoteItem) {
            if (empty($quoteItem['purchase_item_id']) || $quoteItem['item_unit_price'] === null) {
                continue;
            }

            PurchaseItemSupplier::updateOrCreate(
                [
                    'purchase_id' => $purchase->id,
                    'supplier_id' => $data['supplier_id'],
                    'purchase_item_id' => $quoteItem['purchase_item_id'],
                ],
                [
                    'item_unit_price' => $quoteItem['item_unit_price'],
                ]
            );
        }
    }

    protected function getSupplierQuoteTotal(?int $supplierId): float
    {
        $purchase = $this->getOwnerRecord();

        if (! $purchase || ! $supplierId) {
            return 0;
        }

        return (float) PurchaseItemSupplier::query()
            ->where('purchase_id', $purchase->id)
            ->where('supplier_id', $supplierId)
            ->with('purchaseItem')
            ->get()
            ->sum(fn (PurchaseItemSupplier $quote) => ($quote->item_unit_price ?? 0) * ($quote->purchaseItem?->item_quantity ?? 0));
    }

    protected function supplierQuoteModalSchema(bool $isEdit = false): array
    {
        return [
            Select::make('supplier_id')
                ->label('Supplier')
                ->searchable()
                ->options(fn (): array => $isEdit ? $this->getSupplierOptions() : $this->getAvailableSupplierOptions())
                ->required()
                ->dehydrated(fn ($state) => filled($state))
                ->reactive()
                ->afterStateUpdated(function (?int $state, callable $set): void {
                    if (! $state) {
                        $set('quote_items', []);

                        return;
                    }

                    $set('quote_items', $this->buildQuoteItemsForSupplier($state));
                })
                ->disabled($isEdit),
            Repeater::make('quote_items')
                ->label('Quote Items')
                ->reactive()
                ->addable(false)
                ->deletable(false)
                ->reorderable(false)
                ->schema([
                    Hidden::make('purchase_item_id')
                        ->reactive(),
                    TextInput::make('item_name')
                        ->label('Item')
                        ->disabled(),
                    TextInput::make('item_unit_price')
                        ->label('Supplier Unit Price')
                        ->numeric()
                        ->required(),
                ])
                ->columns(3)
                ->disableLabel(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('supplier.supplier_name')
            ->groups([
                Group::make('supplier.supplier_name')
                    ->label('Supplier')
                    ->getTitleFromRecordUsing(function (PurchaseItemSupplier $record): string {
                        $supplierName = $record->supplier?->supplier_name ?? 'Supplier';
                        $total = $this->getSupplierQuoteTotal($record->supplier_id);

                        return sprintf('%s — ₱%s', $supplierName, number_format($total, 2));
                    }),
            ])
            ->defaultGroup('supplier.supplier_name')
            ->collapsedGroupsByDefault()
            ->groupingSettingsHidden()
            ->columns([
                TextColumn::make('purchaseItem.item_name')
                    ->label('Item')
                    ->searchable(),
                TextColumn::make('item_unit_price')
                    ->label('Unit Price')
                    ->money('php')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state === null ? '' : $state),
                TextColumn::make('total_price')
                    ->label('Total Cost')
                    ->money('php')
                    ->sortable()
                    ->getStateUsing(fn (PurchaseItemSupplier $record) => $record->item_unit_price !== null ? $record->item_unit_price * ($record->purchaseItem?->item_quantity ?? 0) : null),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make('addSupplierQuote')
                    ->label('Add Supplier Quote')
                    ->createAnother(false)
                    ->modalHeading('Add Supplier Quote')
                    ->modalSubmitActionLabel('Save Quote')
                    ->modalWidth('xl')
                    ->color('success')
                    ->fillForm(function (): array {
                        return [
                            'quote_items' => $this->buildQuoteItemsForSupplier(null),
                        ];
                    })
                    ->schema($this->supplierQuoteModalSchema())
                    ->action(function (array $data): void {
                        $this->saveSupplierQuoteData($data);
                    }),
            ])
            ->recordActions([
                EditAction::make('editSupplierQuote')
                    ->label('Edit Supplier Quote')
                    ->modalHeading('Edit Supplier Quote')
                    ->modalSubmitActionLabel('Save Quote')
                    ->modalWidth('xl')
                    ->form($this->supplierQuoteModalSchema(true))
                    ->fillForm(function (PurchaseItemSupplier $record): array {
                        return [
                            'supplier_id' => $record->supplier_id,
                            'quote_items' => $this->buildQuoteItemsForSupplier($record->supplier_id),
                        ];
                    })
                    ->action(function (array $data): void {
                        $this->saveSupplierQuoteData($data);
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
