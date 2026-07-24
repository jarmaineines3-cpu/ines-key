<?php

namespace App\Filament\Resources\BACMembers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BACMemberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('school.school_name')
                    ->label('School'),
                TextEntry::make('employee.full_name')
                    ->label('Employee'),
                TextEntry::make('role')
                    ->label('BAC Role'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
