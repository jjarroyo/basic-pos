<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class BackupService
{
    public function runBackup()
    {
        $enabled = Setting::get('backup_enabled', false);
        if (!$enabled) {
            return;
        }

        try {
            $dbPath = database_path('database.sqlite');
            if (!file_exists($dbPath)) {
                Log::error('Backup failed: Database file not found.');
                return;
            }

            $storageType = Setting::get('backup_storage_type', 'local');
            $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sqlite';

            if ($storageType === 'local') {
                $this->backupToLocal($dbPath, $filename);
            } elseif ($storageType === 's3') {
                $this->backupToS3($dbPath, $filename);
            }

        } catch (\Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
        }
    }

    protected function backupToLocal($sourcePath, $filename)
    {
        $targetDir = Setting::get('backup_local_path', storage_path('app/backups'));
        
        // Ensure directory exists
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $filename;
        
        if (copy($sourcePath, $targetPath)) {
            Log::info("Backup created successfully at: {$targetPath}");
        } else {
            Log::error("Failed to copy database to: {$targetPath}");
        }
    }

    protected function backupToS3($sourcePath, $filename)
    {
        // Configure S3 disk dynamically
        $config = [
            'driver' => 's3',
            'key' => Setting::get('backup_s3_access_key'),
            'secret' => Setting::get('backup_s3_secret_key'),
            'region' => Setting::get('backup_s3_default_region', 'us-east-1'),
            'bucket' => Setting::get('backup_s3_bucket'),
            'endpoint' => Setting::get('backup_s3_endpoint'),
            'use_path_style_endpoint' => true, // Often needed for MinIO/DigitalOcean
            'throw' => true,
        ];

        Config::set('filesystems.disks.s3_backup', $config);

        try {
            $fileContent = file_get_contents($sourcePath);
            Storage::disk('s3_backup')->put($filename, $fileContent);
            Log::info("Backup uploaded to S3: {$filename}");
        } catch (\Exception $e) {
            Log::error("S3 Backup failed: " . $e->getMessage());
            throw $e;
        }
    }
}
