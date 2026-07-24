<?php

namespace App\Filament\Resources\BACMembers\Pages;

use App\Filament\Resources\BACMembers\BACMemberResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBACMember extends EditRecord
{
    protected static string $resource = BACMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
