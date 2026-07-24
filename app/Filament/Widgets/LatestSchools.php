<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Schools\SchoolResource;
use App\Models\School;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestSchools extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => School::query()->latest())
            ->columns([
                TextColumn::make('school_id'),
                TextColumn::make('school_name')
                    ->url(fn (School $record): string => SchoolResource::getUrl('view', ['record' => $record])),// Optional: Colors it blue to signify it is a link
                TextColumn::make('school_address'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
