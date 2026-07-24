<?php

namespace App\Filament\Resources\BACMembers;

use App\Filament\Resources\BACMembers\Pages\CreateBACMember;
use App\Filament\Resources\BACMembers\Pages\EditBACMember;
use App\Filament\Resources\BACMembers\Pages\ListBACMembers;
use App\Filament\Resources\BACMembers\Pages\ViewBACMember;
use App\Filament\Resources\BACMembers\Schemas\BACMemberForm;
use App\Filament\Resources\BACMembers\Schemas\BACMemberInfolist;
use App\Filament\Resources\BACMembers\Tables\BACMembersTable;
use App\Models\BACMember;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BACMemberResource extends Resource
{
    protected static ?string $model = BACMember::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $recordTitleAttribute = 'role';

    public static function form(Schema $schema): Schema
    {
        return BACMemberForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BACMemberInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BACMembersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBACMembers::route('/'),
            'create' => CreateBACMember::route('/create'),
            'view' => ViewBACMember::route('/{record}'),
            'edit' => EditBACMember::route('/{record}/edit'),
        ];
    }
}
