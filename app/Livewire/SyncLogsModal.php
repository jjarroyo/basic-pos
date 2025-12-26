<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\File;
use App\Services\SyncService;

class SyncLogsModal extends Component
{
    public $showModal = false;
    public $logs = [];
    public $syncing = false;
    public $syncMessage = '';
    
    protected $listeners = ['openSyncLogs', 'manualSync'];
    
    public function mount()
    {
        $this->loadLogs();
    }
    
    public function openSyncLogs()
    {
        $this->showModal = true;
        $this->loadLogs();
    }
    
    public function closeModal()
    {
        $this->showModal = false;
    }
    
    public function loadLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!File::exists($logFile)) {
            $this->logs = ['⚠️ No hay archivo de logs todavía'];
            return;
        }
        
        // Leer últimas líneas del archivo
        $lines = [];
        $file = new \SplFileObject($logFile, 'r');
        $file->seek(PHP_INT_MAX);
        $lastLine = $file->key();
        $startLine = max(0, $lastLine - 200);
        
        $file->seek($startLine);
        while (!$file->eof()) {
            $line = $file->current();
            if (strpos($line, '[SYNC]') !== false) {
                $lines[] = $line;
            }
            $file->next();
        }
        
        $this->logs = array_slice($lines, -50);
        
        if (empty($this->logs)) {
            $this->logs = ['ℹ️ No hay logs de sincronización todavía'];
        }
    }
    
    public function manualSync()
    {
        $this->syncing = true;
        $this->syncMessage = '';
        
        try {
            $syncService = app(SyncService::class);
            $results = $syncService->syncAll();
            
            $this->syncing = false;
            
            if ($results['online']) {
                $this->syncMessage = '✅ Sincronización completada exitosamente';
            } else {
                $this->syncMessage = '❌ Servidor offline - No se pudo sincronizar';
            }
            
            $this->loadLogs();
            
        } catch (\Exception $e) {
            $this->syncing = false;
            $this->syncMessage = '❌ Error: ' . $e->getMessage();
        }
    }
    
    public function clearLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        if (File::exists($logFile)) {
            File::put($logFile, '');
            $this->logs = ['✅ Logs limpiados'];
            $this->syncMessage = '✅ Logs limpiados exitosamente';
        }
    }
    
    public function render()
    {
        return view('livewire.sync-logs-modal');
    }
}
