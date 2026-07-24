<?php

namespace App\Filament\Resources\UacsCodes\Pages;

use App\Filament\Resources\UacsCodes\UacsCodeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageUacsCodes extends ManageRecords
{
    protected static string $resource = UacsCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
