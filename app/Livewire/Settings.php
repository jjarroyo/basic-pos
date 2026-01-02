<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse; 

class Settings extends Component
{
    use WithFileUploads;

    public $company_name;
    public $company_nit;
    public $company_address;
    public $company_city;
    public $company_phone;
    public $company_email;
    public $company_website;
    public $ticket_footer;
    public $tax_rate;
    public $currency_symbol;
    public $logo;
    public $current_logo;
    public $backupFile;
    public $enable_cash_drawer;
    public $cash_drawer_command;

    // Backup Properties
    public $backup_enabled;
    public $backup_frequency;
    public $backup_time;
    public $backup_storage_type;
    public $backup_local_path;
    public $backup_s3_access_key;
    public $backup_s3_secret_key;
    public $backup_s3_region;
    public $backup_s3_bucket;
    public $backup_s3_endpoint;

    // Email Notification Properties
    public $email_notifications_enabled;
    public $email_notifications_recipient;

    public function mount()
    {
        $this->company_name = Setting::get('company_name');
        $this->company_nit = Setting::get('company_nit');
        $this->company_address = Setting::get('company_address');
        $this->company_city = Setting::get('company_city');
        $this->company_phone = Setting::get('company_phone');
        $this->company_email = Setting::get('company_email');
        $this->company_website = Setting::get('company_website');
        $this->ticket_footer = Setting::get('ticket_footer');
        $this->tax_rate = Setting::get('tax_rate', 19);
        $this->currency_symbol = Setting::get('currency_symbol', '$');
        $this->current_logo = Setting::get('company_logo');
        $this->enable_cash_drawer = filter_var(Setting::get('enable_cash_drawer', false), FILTER_VALIDATE_BOOLEAN);
        $this->cash_drawer_command = Setting::get('cash_drawer_command', '\x1B\x70\x00\x19\xFA');

        // Backup Settings initialization
        $this->backup_enabled = filter_var(Setting::get('backup_enabled', false), FILTER_VALIDATE_BOOLEAN);
        $this->backup_frequency = Setting::get('backup_frequency', 'startup');
        $this->backup_time = Setting::get('backup_time', '00:00');
        $this->backup_storage_type = Setting::get('backup_storage_type', 'local');
        $this->backup_local_path = Setting::get('backup_local_path', storage_path('app/backups'));
        $this->backup_s3_access_key = Setting::get('backup_s3_access_key');
        $this->backup_s3_secret_key = Setting::get('backup_s3_secret_key');
        $this->backup_s3_region = Setting::get('backup_s3_default_region', 'us-east-1');
        $this->backup_s3_bucket = Setting::get('backup_s3_bucket');
        $this->backup_s3_endpoint = Setting::get('backup_s3_endpoint');

        // Email Notification Settings
        $this->email_notifications_enabled = filter_var(Setting::get('email_notifications_enabled', false), FILTER_VALIDATE_BOOLEAN);
        $this->email_notifications_recipient = Setting::get('email_notifications_recipient');
    }

    public function save()
    {
        $this->validate([
            'company_name' => 'required|string|max:255',
            'company_nit' => 'nullable|string|max:50',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'logo' => 'nullable|image|max:2048',
            'backup_local_path' => 'required_if:backup_storage_type,local',
            'backup_s3_access_key' => 'required_if:backup_storage_type,s3',
            'backup_s3_secret_key' => 'required_if:backup_storage_type,s3',
            'backup_s3_bucket' => 'required_if:backup_storage_type,s3',
            'email_notifications_recipient' => 'required_if:email_notifications_enabled,true|nullable|email',
        ]);

        Setting::set('company_name', $this->company_name);
        Setting::set('company_nit', $this->company_nit);
        Setting::set('company_address', $this->company_address);
        Setting::set('company_city', $this->company_city);
        Setting::set('company_phone', $this->company_phone);
        Setting::set('company_email', $this->company_email);
        Setting::set('company_website', $this->company_website);
        Setting::set('ticket_footer', $this->ticket_footer);
        Setting::set('tax_rate', $this->tax_rate);
        Setting::set('currency_symbol', $this->currency_symbol);
        Setting::set('enable_cash_drawer', $this->enable_cash_drawer ? '1' : '0');
        Setting::set('cash_drawer_command', $this->cash_drawer_command);

        // Save Backup Settings
        Setting::set('backup_enabled', $this->backup_enabled ? '1' : '0');
        Setting::set('backup_frequency', $this->backup_frequency);
        Setting::set('backup_time', $this->backup_time);
        Setting::set('backup_storage_type', $this->backup_storage_type);
        Setting::set('backup_local_path', $this->backup_local_path);
        Setting::set('backup_s3_access_key', $this->backup_s3_access_key);
        Setting::set('backup_s3_secret_key', $this->backup_s3_secret_key);
        Setting::set('backup_s3_default_region', $this->backup_s3_region);
        Setting::set('backup_s3_bucket', $this->backup_s3_bucket);
        Setting::set('backup_s3_endpoint', $this->backup_s3_endpoint);

        // Save Email Notification Settings
        Setting::set('email_notifications_enabled', $this->email_notifications_enabled ? '1' : '0');
        Setting::set('email_notifications_recipient', $this->email_notifications_recipient);

        if ($this->logo) {
            $logoPath = $this->logo->store('logos', 'public');
            Setting::set('company_logo', $logoPath);
            $this->current_logo = $logoPath;
        }

        session()->flash('message', 'Configuración guardada correctamente.');
    }

    public function testS3Connection()
    {
        $this->validate([
            'backup_s3_access_key' => 'required',
            'backup_s3_secret_key' => 'required',
            'backup_s3_bucket' => 'required',
            'backup_s3_region' => 'required',
        ]);

        $config = [
            'driver' => 's3',
            'key' => $this->backup_s3_access_key,
            'secret' => $this->backup_s3_secret_key,
            'region' => $this->backup_s3_region,
            'bucket' => $this->backup_s3_bucket,
            'endpoint' => $this->backup_s3_endpoint,
            'use_path_style_endpoint' => true,
            'throw' => true,
        ];

        try {
            \Illuminate\Support\Facades\Config::set('filesystems.disks.s3_test', $config);
            Storage::disk('s3_test')->put('test_connection.txt', 'Connection Successful');
            Storage::disk('s3_test')->delete('test_connection.txt');
            
            session()->flash('message', 'Conexión S3 exitosa.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error de conexión S3: ' . $e->getMessage());
        }
    }

    public function createBackup()
    {
        try {
            $dbPath = database_path('database.sqlite');
            
            if (!file_exists($dbPath)) {
                session()->flash('error', 'No se encontró la base de datos.');
                return;
            }

            $timestamp = now()->format('Y-m-d_H-i-s');
            $backupName = "backup_{$timestamp}.sqlite";
            
            return response()->download($dbPath, $backupName, [
                'Content-Type' => 'application/x-sqlite3',
            ]);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el backup: ' . $e->getMessage());
        }
    }

    public function restore()
    {
        $this->validate([
            'backupFile' => 'required|file|mimes:sqlite,db|max:51200', // 50MB max
        ]);

        try {
            $dbPath = database_path('database.sqlite');
            
            // Create automatic backup before restore
            $autoBackupName = 'auto_backup_before_restore_' . now()->format('Y-m-d_H-i-s') . '.sqlite';
            $backupDir = storage_path('app/backups');
            
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            
            copy($dbPath, $backupDir . '/' . $autoBackupName);

            // Validate uploaded file is a valid SQLite database
            $uploadedPath = $this->backupFile->getRealPath();
            
            try {
                $testDb = new \PDO('sqlite:' . $uploadedPath);
                $testDb->query('SELECT 1');
                $testDb = null;
            } catch (\Exception $e) {
                session()->flash('error', 'El archivo no es una base de datos SQLite válida.');
                return;
            }

            // Close all database connections
            DB::disconnect();

            // Replace current database with backup
            copy($uploadedPath, $dbPath);

            // Reconnect to database
            DB::reconnect();

            session()->flash('message', 'Base de datos restaurada correctamente. Se creó un backup automático en storage/app/backups.');
            $this->backupFile = null;
            
            // Reload settings
            $this->mount();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al restaurar el backup: ' . $e->getMessage());
        }
    }

    public function getBackupInfo()
    {
        $dbPath = database_path('database.sqlite');
        
        if (!file_exists($dbPath)) {
            return null;
        }

        return [
            'size' => $this->formatBytes(filesize($dbPath)),
            'modified' => date('Y-m-d H:i:s', filemtime($dbPath)),
        ];
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function render()
    {
        return view('livewire.settings', [
            'backupInfo' => $this->getBackupInfo(),
        ]);
    }
}