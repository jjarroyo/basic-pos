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
    }

    public function save()
    {
        $this->validate([
            'company_name' => 'required|string|max:255',
            'company_nit' => 'nullable|string|max:50',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'logo' => 'nullable|image|max:2048',
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

        if ($this->logo) {
            $logoPath = $this->logo->store('logos', 'public');
            Setting::set('company_logo', $logoPath);
            $this->current_logo = $logoPath;
        }

        session()->flash('message', 'Configuración guardada correctamente.');
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