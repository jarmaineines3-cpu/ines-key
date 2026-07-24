<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('employee_no')
                    ->label('Employee No')
                    ->required(),
                TextInput::make('fname')
                    ->required()
                    ->label('First Name'),
                TextInput::make('mname')
                    ->required()
                    ->label('Middle Name'),
                TextInput::make('lname')
                    ->required()
                    ->label('last Name'),
                TextInput::make('xname')
                    ->label('Extension Name (Optional)'),
                Select::make('sex')
                    ->required()
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ]),
                DatePicker::make('birthday')
                    ->required(),
                TextInput::make('address')
                    ->required(),
            ]);
    }
}
