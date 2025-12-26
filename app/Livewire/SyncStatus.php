<?php

namespace App\Livewire;

use App\Services\SyncService;
use Livewire\Component;

class SyncStatus extends Component
{
    public $isOnline = false;
    public $pendingSales = 0;
    public $pendingSessions = 0;
    public $lastSync = null;
    public $mode = 'standalone';

    public function mount()
    {
        $this->mode = config('pos.mode');
        $this->checkStatus();
    }

    public function checkStatus()
    {
        if ($this->mode !== 'client') {
            return;
        }

        $sync = app(SyncService::class);
        
        $this->isOnline = $sync->isServerOnline();
        $this->pendingSales = \App\Models\Sale::whereNull('synced_at')->count();
        $this->pendingSessions = \App\Models\CashRegisterSession::whereNull('synced_at')
            ->where('status', 'closed')
            ->count();
        
        $this->lastSync = cache('last_sync_products') ?? cache('last_sync_users');
    }

    public function syncNow()
    {
        if ($this->mode !== 'client') {
            session()->flash('error', 'Solo disponible en modo cliente');
            return;
        }

        $sync = app(SyncService::class);
        $results = $sync->syncAll();

        if ($results['online']) {
            session()->flash('message', '✅ Sincronización completada');
        } else {
            session()->flash('error', '❌ Servidor no disponible');
        }

        $this->checkStatus();
    }

    public function render()
    {
        return view('livewire.sync-status');
    }
}
