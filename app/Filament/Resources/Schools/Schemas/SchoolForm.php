<?php

namespace App\Filament\Resources\Schools\Schemas;

use App\Models\Employee;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SchoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('school_id')
                    ->required(),
                TextInput::make('school_name')
                    ->required(),
                Select::make('school_head')
                    ->options(
                        Employee::pluck('full_name','id')
                    ),
                FileUpload::make('school_logo')
                    ->label('School Logo')
                    ->image()
                    ->disk('public')
                    ->directory('school-logos')
                    ->visibility('public')
                    ->helperText('Uploaded files are saved using the format school_id_school_name.ext and will overwrite any existing logo with the same name.')
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, $get): string {
                        $schoolId = trim((string) ($get('school_id') ?? '')) ?: 'school';
                        $schoolName = trim((string) ($get('school_name') ?? '')) ?: 'school';

                        return sprintf(
                            '%s_%s.%s',
                            $schoolId,
                            Str::slug($schoolName, '_'),
                            $file->getClientOriginalExtension(),
                        );
                    })
                    ->deleteUploadedFileUsing(function (string $file): void {
                        Storage::disk('public')->delete($file);
                    }),
                TextInput::make('school_address')
                    ->required(),
                TextInput::make('school_contact')
                    ->label('Contact Number')
                    ->tel()
                    ->required(),
                TextInput::make('school_email')
                    ->email()
                    ->required(),
                TextInput::make('school_social')
                    ->label('Social Media Handle')
                    ->required(),
            ]);
    }
}
