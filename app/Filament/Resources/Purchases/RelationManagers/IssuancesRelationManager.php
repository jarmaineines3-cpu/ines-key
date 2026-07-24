<?php

namespace App\Filament\Resources\Purchases\RelationManagers;


use App\Models\Employee;
use App\Models\PurchaseIssuance;
use App\Models\PurchaseItem;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextColumn;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn as TableTextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class IssuancesRelationManager extends RelationManager
{
    protected static string $relationship = 'issuances';

    

    public function isReadOnly(): bool
    {
        return false;
    }

    protected function getAvailablePurchaseItems(?int $employeeId = null, ?int $currentPurchaseItemId = null): array
    {
        $purchase = $this->getOwnerRecord();

        if (! $purchase) {
            return [];
        }

        return PurchaseItem::query()
            ->where('purchase_id', $purchase->id)
            ->get()
            ->mapWithKeys(function (PurchaseItem $item) use ($currentPurchaseItemId) {
                $remaining = $this->getRemainingQuantity($item->id);
                if ($remaining <= 0 && $item->id !== $currentPurchaseItemId) {
                    return [];
                }

                return [$item->id => sprintf('%s (%s) — available: %d', $item->item_name, $item->item_unit, $remaining)];
            })
            ->toArray();
    }

    protected function getIssuedQuantity(int $purchaseItemId): int
    {
        return PurchaseIssuance::query()
            ->where('purchase_item_id', $purchaseItemId)
            ->sum('quantity');
    }

    protected function isInventoryPurchaseItem(?int $purchaseItemId): bool
    {
        if (! $purchaseItemId) {
            return false;
        }

        return optional(PurchaseItem::with('item')->find($purchaseItemId)->item)->inventory_item ?? false;
    }

    protected function getRemainingQuantity(int $purchaseItemId): int
    {
        $purchaseItem = PurchaseItem::find($purchaseItemId);

        if (! $purchaseItem) {
            return 0;
        }

        return max($purchaseItem->item_quantity - $this->getIssuedQuantity($purchaseItemId), 0);
    }

    protected function shouldShowInventoryFields(?int $purchaseItemId, mixed $quantity): bool
    {
        if (! $purchaseItemId) {
            return false;
        }

        if (! is_numeric($quantity)) {
            return false;
        }

        $quantity = (int) $quantity;

        if ($quantity <= 0) {
            return false;
        }

        return $this->isInventoryPurchaseItem($purchaseItemId);
    }

    protected function getInspectionDateDefault(): ?string
    {
        $purchase = $this->getOwnerRecord();

        if (! $purchase) {
            return null;
        }

        $inspectionDate = $purchase->inspection()?->value('inspection_date') ?? $purchase->inspection_date ?? null;

        if (blank($inspectionDate)) {
            return null;
        }

        return \Illuminate\Support\Carbon::parse($inspectionDate)->toDateString();
    }

    protected function getEmployeeOptions(): array
    {
        $purchase = $this->getOwnerRecord();
        $schoolId = $purchase?->school;

        if (! $schoolId) {
            return [];
        }

        return Employee::query()
            ->whereHas('schools', function ($query) use ($schoolId): void {
                $query->where('schools.id', $schoolId);
            })
            ->orderBy('full_name')
            ->pluck('full_name', 'id')
            ->toArray();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                    ->label('Employee')
                    ->options(fn (): array => $this->getEmployeeOptions())
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set) {
                        // Reset the selected purchase item when employee changes
                        $set('purchase_item_id', null);
                        $set('remaining_quantity', 0);
                    }),
                Select::make('purchase_item_id')
                    ->label('Purchase Item')
                    ->reactive()
                    ->options(fn (callable $get): array => $this->getAvailablePurchaseItems($get('employee_id'), $get('purchase_item_id')))
                    ->required()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if (! $state) {
                            return;
                        }

                        $remaining = $this->getRemainingQuantity($state);
                        $set('remaining_quantity', $remaining);
                    }),
                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->minValue(1)
                    ->maxValue(function ($state, callable $get) {
                        $purchaseItemId = $get('purchase_item_id');

                        if (! $purchaseItemId) {
                            return 0;
                        }

                        $remaining = $this->getRemainingQuantity($purchaseItemId);

                        return $remaining > 0 ? $remaining : null;
                    }),
                TextInput::make('inventory_lifespan')
                    ->label('Inventory Lifespan')
                    ->numeric()
                    ->minValue(1)
                    ->visible(fn (callable $get): bool => $this->shouldShowInventoryFields($get('purchase_item_id'), $get('quantity')))
                    ->required(fn (callable $get): bool => $this->shouldShowInventoryFields($get('purchase_item_id'), $get('quantity'))),
                
                Textarea::make('inventory_details')
                    ->label('Inventory Details')
                    ->placeholder('Add details about the inventory item')
                    ->rows(3)
                    ->visible(fn (callable $get): bool => $this->shouldShowInventoryFields($get('purchase_item_id'), $get('quantity'))),
                TextInput::make('ics_no')
                    ->label('ICS No')
                    ->disabled()
                    ->default(''),
                DatePicker::make('issued_at')
                    ->label('Issued At')
                    ->required()
                    ->default(fn (): ?string => $this->getInspectionDateDefault()),
                TextInput::make('remaining_quantity')
                    ->label('Available Quantity')
                    ->disabled()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
        ->defaultPaginationPageOption(100)
            ->groups([
                Group::make('employee.full_name')
                    ->label('Employee')
                    ->collapsible(),
            ])
            ->defaultGroup('employee.full_name')
            ->collapsedGroupsByDefault()
            ->recordTitleAttribute('id')
            ->columns([
                TableTextColumn::make('purchaseItem.item_name')
                    ->label('Item')
                    ->searchable(),
                TableTextColumn::make('ris_no')
                    ->label('RIS No')
                    ->searchable(),
                TableTextColumn::make('inventory_no')
                    ->label('Inventory No')
                    ->searchable(),
                TableTextColumn::make('ics_no')
                    ->label('ICS No')
                    ->searchable(),
                TableTextColumn::make('quantity')
                    ->label('Issued Qty')
                    ->sortable(),
                TableTextColumn::make('inventory_lifespan')
                    ->label('Lifespan')
                    ->sortable(),
                TableTextColumn::make('issued_at')
                    ->label('Date Issued')
                    ->date()
                    ->sortable(),
                TableTextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make('addIssuance')
                    ->label('Add Issuance')
                    ->modalHeading('Create Issuance')
                    ->modalSubmitActionLabel('Save Issuance')
                    ->color('success'),
                CreateAction::make('bulkIssue')
                    ->label('Bulk Issue')
                    ->modalHeading('Issue Multiple Items')
                    ->modalWidth('2xl')
                    ->modalSubmitActionLabel('Issue')
                    ->form(function () {
                        $purchase = $this->getOwnerRecord();

                        $components = [
                            Select::make('employee_id')
                                ->label('Employee')
                                ->options(fn (): array => $this->getEmployeeOptions())
                                ->required()
                                ->reactive(),
                            DatePicker::make('issued_at')
                                ->label('Issued At')
                                ->required()
                                ->default(fn (): ?string => $this->getInspectionDateDefault()),
                        ];

                        if ($purchase) {
                            foreach ($purchase->purchaseItems as $item) {
                                $remaining = $this->getRemainingQuantity($item->id);
                                $quantityFieldName = 'qty_' . $item->id;

                                $components[] = TextInput::make($quantityFieldName)
                                    ->label(sprintf('%s (%s) — available: %d', $item->item_name, $item->item_unit, $remaining))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue($remaining)
                                    ->default(0)
                                    ->reactive();

                                if (optional($item->item)->inventory_item) {
                                    $components[] = TextInput::make('lifespan_' . $item->id)
                                        ->label(sprintf('Inventory Lifespan for %s', $item->item_name))
                                        ->numeric()
                                        ->minValue(1)
                                        ->default(1)
                                        ->visible(function (callable $get) use ($item, $quantityFieldName): bool {
                                            return $this->shouldShowInventoryFields($item->id, $get($quantityFieldName));
                                        });

                                    $components[] = Textarea::make('details_' . $item->id)
                                        ->label(sprintf('Inventory Details for %s', $item->item_name))
                                        ->placeholder('Add details about the inventory item')
                                        ->rows(3)
                                        ->default('')
                                        ->visible(function (callable $get) use ($item, $quantityFieldName): bool {
                                            return $this->shouldShowInventoryFields($item->id, $get($quantityFieldName));
                                        });
                                }
                            }
                        }

                        return $components;
                    })
                    ->action(function (array $data) {
                        $purchase = $this->getOwnerRecord();
                        if (! $purchase) {
                            throw new \Exception('Purchase not found.');
                        }

                        $employeeId = $data['employee_id'] ?? null;
                        $issuedAt = $data['issued_at'] ?? null;

                        if (! $employeeId) {
                            throw new \Exception('Employee is required.');
                        }

                        foreach ($purchase->purchaseItems as $item) {
                            $key = 'qty_' . $item->id;
                            $qty = isset($data[$key]) ? intval($data[$key]) : 0;
                            if ($qty <= 0) {
                                continue;
                            }

                            $remaining = $this->getRemainingQuantity($item->id);
                            if ($qty > $remaining) {
                                throw new \Exception(sprintf('Requested quantity for "%s" exceeds available (%d).', $item->item_name, $remaining));
                            }

                            $issuanceData = [
                                'purchase_id' => $purchase->id,
                                'purchase_item_id' => $item->id,
                                'employee_id' => $employeeId,
                                'quantity' => $qty,
                                'issued_at' => $issuedAt,
                            ];

                            if (optional($item->item)->inventory_item) {
                                $issuanceData['inventory_lifespan'] = isset($data['lifespan_' . $item->id])
                                    ? intval($data['lifespan_' . $item->id])
                                    : null;
                                $issuanceData['inventory_details'] = $data['details_' . $item->id] ?? null;
                            }

                            PurchaseIssuance::create($issuanceData);
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}

