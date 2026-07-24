<?php

namespace App\Filament\Resources\Purchases\RelationManagers;

use App\Models\PurchasePicture;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PicturesRelationManager extends RelationManager
{
    protected static string $relationship = 'pictures';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->components([
            FileUpload::make('image_path')
                ->label('Picture')
                //->hiddenOn('edit')
                ->image()
                ->imageEditor() // This enables the built-in cropping/editing tool
                ->directory(fn () => 'purchases/' . $this->getOwnerRecord()?->getKey())
                ->disk('public')
                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                    $extension = $file->getClientOriginalExtension() ?: 'jpg';
                    return 'picture-' . now()->timestamp . '-' . uniqid() . '.' . $extension;
                }),
            TextInput::make('caption')
                ->label('Caption')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('caption')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Picture')
                    ->disk('public'),
                TextColumn::make('caption')
                    ->label('Caption')
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
