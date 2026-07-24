<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('supplier_name')
                    ->required(),
                TextInput::make('supplier_address')
                    ->required(),
                TextInput::make('supplier_tin')
                    ->required(),
                TextInput::make('supplier_contact_number')
                    ->required(),
            ]);
    }
}
