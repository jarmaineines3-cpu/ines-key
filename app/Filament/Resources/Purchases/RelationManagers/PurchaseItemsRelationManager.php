<?php

namespace App\Filament\Resources\Purchases\RelationManagers;

use App\Filament\Resources\Purchases\PurchaseResource;
use App\Models\Item;
use App\Models\PurchaseItem;
use App\Models\School;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchaseItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'purchaseItems';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('item_id')
                    ->label('Item')
                    ->searchable()
                    ->live()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->label('Item Name'),
                        TextInput::make('unit')
                            ->required()
                            ->label('Unit'),
                        TextInput::make('item_unit_price')
                            ->label('Item Unit Price')
                            ->numeric()
                            ->required(),
                        TextInput::make('stock_no')
                            ->label('Stock No.')
                            ->numeric(),
                    ])
                    ->createOptionUsing(function (array $data) {
                        $purchase = $this->getOwnerRecord();
                        $schoolId = $purchase?->school;
                        if ($schoolId instanceof School) {
                            $schoolId = $schoolId->id;
                        }

                        $item = Item::create([
                            'school_id' => $schoolId,
                            'stock_no' => $data['stock_no'] ?? null,
                            'name' => $data['name'],
                            'unit' => $data['unit'],
                            'item_unit_price' => $data['item_unit_price'],
                        ]);

                        return $item->id;
                    })
                    ->createOptionModalHeading('Create Item')
                    ->options(function () {
                        $purchase = $this->getOwnerRecord();
                        $schoolId = $purchase?->school;
                        if ($schoolId instanceof School) {
                            $schoolId = $schoolId->id;
                        }

                        return Item::query()
                            ->when($schoolId, fn ($query, $schoolId) => $query->where('school_id', $schoolId))
                            ->orderBy('name')
                            ->pluck('name', 'id');
                    })
                    ->rules([
                        function ($attribute, $value, $fail) {
                            $purchase = $this->getOwnerRecord();

                            $exists = PurchaseItem::query()
                                ->where('purchase_id', $purchase?->id)
                                ->where('item_id', $value)
                                ->when($this->getOwnerRecord()->id ?? null, fn ($query, $purchaseId) => $query->where('id', '!=', $this->getOwnerRecord()->id))
                                ->exists();

                            if ($exists) {
                                $fail('This item has already been added to this purchase.');
                            }
                        },
                    ])
                    ->afterStateUpdated(function ($state, Set $set) {
                        if (! $state) {
                            $set('item_unit_price', 0);

                            return;
                        }

                        $item = Item::find($state);

                        if ($item) {
                            $set('item_unit_price', $item->item_unit_price);
                            $set('item_name', $item->name);
                            $set('item_unit', $item->unit);
                        }
                    })
                    ->required(),
                TextInput::make('item_quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->required(),
                TextInput::make('item_unit_price')
                    ->label('Item Unit Price')
                    ->numeric()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('item_name')
            ->columns([
                TextColumn::make('item.name')
                    ->label('Item')
                    ->searchable(),
                TextColumn::make('item_name')
                    ->label('Item Name')
                    ->searchable(),
                TextColumn::make('item_unit')
                    ->label('Unit')
                    ->searchable(),
                TextColumn::make('item_unit_price')
                    ->label('Unit Price')
                    ->money('php')
                    ->sortable(),
                TextColumn::make('total_price')
                    ->label('Total Price')
                    ->money('php')
                    ->sortable()
                    ->getStateUsing(fn (PurchaseItem $record) => $record->item_unit_price * $record->item_quantity),
                TextColumn::make('item_quantity')
                    ->label('Quantity')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->color('success'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                //DetachAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
                //DetachBulkAction::make(),
            ]);
    }
}
