<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class SyncLogs extends Component
{
    public $logs = [];
    public $autoRefresh = false;
    
    public function mount()
    {
        $this->loadLogs();
    }
    
    public function loadLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!File::exists($logFile)) {
            $this->logs = ['⚠️ No hay archivo de logs todavía'];
            return;
        }
        
        // Leer últimas 100 líneas del archivo
        $lines = [];
        $file = new \SplFileObject($logFile, 'r');
        $file->seek(PHP_INT_MAX);
        $lastLine = $file->key();
        $startLine = max(0, $lastLine - 200); // Leer últimas 200 líneas
        
        $file->seek($startLine);
        while (!$file->eof()) {
            $line = $file->current();
            // Solo mostrar líneas que contengan [SYNC]
            if (strpos($line, '[SYNC]') !== false) {
                $lines[] = $line;
            }
            $file->next();
        }
        
        // Tomar solo las últimas 50 líneas SYNC
        $this->logs = array_slice($lines, -50);
        
        if (empty($this->logs)) {
            $this->logs = ['ℹ️ No hay logs de sincronización todavía'];
        }
    }
    
    public function refresh()
    {
        $this->loadLogs();
    }
    
    public function clearLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        if (File::exists($logFile)) {
            File::put($logFile, '');
            $this->logs = ['✅ Logs limpiados'];
        }
    }
    
    public function render()
    {
        return view('livewire.sync-logs');
    }
}
