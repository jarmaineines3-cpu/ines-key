<?php

namespace App\Filament\Resources\Items;

use App\Filament\Resources\Items\Pages\ManageItems;
use App\Models\Item;
use App\Models\School;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('unit')
                    ->required(),
                Select::make('uacs_code_id')
                    ->label('UACS Code')
                    ->relationship('uacsCode', 'account_title')
                    ->searchable()
                    ->preload(),
                Toggle::make('inventory_item')
                    ->label('Inventory Item')
                    ->default(false),
                TextInput::make('item_unit_price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                Select::make('school_id')
                    ->label('School')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (?string $state, Set $set): void {
                        if (! $state) {
                            $set('stock_no', '');

                            return;
                        }

                        $existingCount = Item::query()
                            ->where('school_id', $state)
                            ->count('*');

                        $nextStockNo = $existingCount + 1;
                        $set('stock_no', str_pad((string) $nextStockNo, 3, '0', STR_PAD_LEFT));
                    })
                    ->options(School::query()->pluck('school_name', 'id')),
                TextInput::make('stock_no')
                    ->label('Stock No.')
                    ->required()
                    ->afterStateHydrated(function ($state, callable $set, callable $get): void {
                        $schoolId = $get('school_id');

                        if (! $schoolId) {
                            return;
                        }

                        $existingCount = Item::query()
                            ->where('school_id', $schoolId)
                            ->count('*');

                        $nextStockNo = $existingCount + 1;
                        $set('stock_no', str_pad((string) $nextStockNo, 3, '0', STR_PAD_LEFT));
                    })
                    ->formatStateUsing(function ($state, $get): string {
                        if (filled($state)) {
                            return $state;
                        }

                        $schoolId = $get('school_id');

                        if (! $schoolId) {
                            return '';
                        }

                        $existingCount = Item::query()
                            ->where('school_id', $schoolId)
                            ->count('*');

                        $nextStockNo = $existingCount + 1;

                        return str_pad((string) $nextStockNo, 3, '0', STR_PAD_LEFT);
                    }),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('unit'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('item_unit_price')
                    ->money('PHP', true),
                TextEntry::make('school.school_name')
                    ->label('School')
                    ->placeholder('-'),
                TextEntry::make('uacsCode.uacs_code')
                    ->label('UACS Code')
                    ->placeholder('-'),
                TextEntry::make('uacsCode.account_title')
                    ->label('Account Title')
                    ->placeholder('-'),
                TextEntry::make('stock_no')
                    ->numeric()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('inventory_item')
                    ->label('Inventory Item')
                    ->boolean() // This automatically handles the true/false logic
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('unit')
                    ->searchable(),
                TextColumn::make('item_unit_price')
                    ->money('PHP', true)
                    ->sortable(),
                TextColumn::make('school.school_name')
                    ->searchable(),
                TextColumn::make('uacsCode.uacs_code')
                    ->label('UACS Code')
                    ->searchable(),
                TextColumn::make('uacsCode.account_title')
                    ->label('Account Title')
                    ->searchable(),
                TextColumn::make('stock_no')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageItems::route('/'),
        ];
    }
}
