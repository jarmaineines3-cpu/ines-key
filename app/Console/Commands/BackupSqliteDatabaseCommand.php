<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupSqliteDatabaseCommand extends Command
{
    protected $signature = 'db:backup-sqlite';

    protected $description = 'Create a backup copy of the SQLite database file.';

    public function handle(): int
    {
        $databasePath = database_path('database.sqlite');

        if (! file_exists($databasePath)) {
            $this->error('SQLite database file not found at: ' . $databasePath);

            return self::FAILURE;
        }

        $backupDirectory = storage_path('app/backups');

        if (! is_dir($backupDirectory)) {
            mkdir($backupDirectory, 0755, true);
        }

        $backupPath = $backupDirectory . DIRECTORY_SEPARATOR . 'sqlite_backup_' . now()->format('Ymd_His') . '.sqlite';
        copy($databasePath, $backupPath);

        $this->info('SQLite database backup created at: ' . $backupPath);

        return self::SUCCESS;
    }
}
