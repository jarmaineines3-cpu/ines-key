<?php

namespace App\Filament\Resources\BACMembers\Pages;

use App\Filament\Resources\BACMembers\BACMemberResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBACMember extends ViewRecord
{
    protected static string $resource = BACMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
