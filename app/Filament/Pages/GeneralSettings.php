<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Inerba\DbConfig\AbstractPageSettings;

class GeneralSettings extends AbstractPageSettings
{
    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    protected static ?string $title = 'Settings';

    protected function settingName(): string
    {
        return 'general';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Administration';
    }

    /**
     * Provide default values.
     *
     * @return array<string, mixed>
     */
    public function getDefaultData(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backupDatabase')
                ->label('Backup Database')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Backup SQLite Database')
                ->modalDescription('Create a manual backup copy of the current SQLite database.')
                ->modalSubmitActionLabel('Backup Now')
                ->visible(fn (): bool => Auth::user()?->hasRole('super_admin') ?? false)
                ->action(function (): void {
                    $user = Auth::user();

                    if (! $user?->hasRole('super_admin')) {
                        Notification::make()
                            ->title('Only the super admin can create a database backup.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $exitCode = Artisan::call('db:backup-sqlite');

                    if ($exitCode === 0) {
                        Notification::make()
                            ->title('Database backup created successfully.')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Database backup failed.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('site_name')->label('Site Name'),
                TextInput::make('site_version')->label('Site Version'),
                TextInput::make('contact_email')->label('Contact Email'),
                Toggle::make('maintenance_mode')->label('Maintenance Mode'),
            ])
            ->statePath('data');
    }
}
