<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--compress : Compress the backup file}';
    protected $description = 'Create a backup of the database';

    public function handle()
    {
        $this->info('Creating database backup...');

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$database}_{$timestamp}.sql";
        
        if ($this->option('compress')) {
            $filename .= '.gz';
            $command = "mysqldump --host={$host} --user={$username} --password={$password} {$database} | gzip > " . storage_path("app/backups/{$filename}");
        } else {
            $command = "mysqldump --host={$host} --user={$username} --password={$password} {$database} > " . storage_path("app/backups/{$filename}");
        }

        // Create backups directory if it doesn't exist
        if (!Storage::exists('backups')) {
            Storage::makeDirectory('backups');
        }

        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->info("Database backup created successfully: {$filename}");
            
            // Upload to S3 if configured
            if (config('filesystems.default') === 's3') {
                Storage::disk('s3')->put("backups/{$filename}", Storage::get("backups/{$filename}"));
                $this->info("Backup uploaded to S3");
            }
            
            // Clean up old local backups (keep last 7 days)
            $this->cleanupOldBackups();
            
        } else {
            $this->error('Database backup failed');
            return 1;
        }

        return 0;
    }

    private function cleanupOldBackups()
    {
        $backupFiles = Storage::files('backups');
        $cutoffDate = now()->subDays(7);

        foreach ($backupFiles as $file) {
            $fileTime = Storage::lastModified($file);
            if ($fileTime < $cutoffDate->timestamp) {
                Storage::delete($file);
                $this->line("Deleted old backup: " . basename($file));
            }
        }
    }
}
