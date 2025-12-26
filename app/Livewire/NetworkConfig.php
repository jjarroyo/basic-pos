<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class NetworkConfig extends Component
{
    public $showModal = false;
    public $mode = 'standalone';
    public $serverIp = '';
    public $serverPort = 8000;
    public $localIp = '';
    public $connectionStatus = '';
    public $serverRunning = false;

    public function mount()
    {
        // Load current configuration
        $this->mode = config('pos.mode', 'standalone');
        $this->serverIp = config('pos.server_ip', '');
        $this->serverPort = config('pos.server_port', 8000);
        
        // Get local IP
        $this->localIp = $this->getLocalIp();
        if ($this->mode === 'server') {
            $this->checkServerStatus();
        }
    }

    public function checkServerStatus()
    {
        // Lista de direcciones a probar para ver si el servidor responde
        $urlsToTest = [
            'http://127.0.0.1:' . $this->serverPort . '/api/health',
            'http://localhost:' . $this->serverPort . '/api/health',
        ];

        // Si tenemos una IP local detectada, probarla también
        if (!empty($this->localIp)) {
            $urlsToTest[] = 'http://' . $this->localIp . ':' . $this->serverPort . '/api/health';
        }

        $this->serverRunning = false;

        foreach ($urlsToTest as $url) {
            try {
                // Aumentamos el timeout a 2 segundos para dar tiempo a responder
                $response = Http::timeout(2)->get($url);
                
                if ($response->successful()) {
                    $this->serverRunning = true;
                    // Si responde una, ya no probamos las demás
                    return; 
                }
            } catch (\Exception $e) {
                // Si falla esta URL, el loop continuará con la siguiente
                continue;
            }
        }
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->connectionStatus = '';
    }

    public function testConnection()
    {
        if (empty($this->serverIp)) {
            $this->connectionStatus = 'error';
            session()->flash('error', 'Por favor ingresa la IP del servidor');
            return;
        }

        try {
            $url = 'http://' . $this->serverIp . ':' . $this->serverPort . '/api/health';
            $response = Http::timeout(3)->get($url);

            if ($response->successful()) {
                $this->connectionStatus = 'success';
                session()->flash('message', '✅ Conexión exitosa con el servidor');
            } else {
                $this->connectionStatus = 'error';
                session()->flash('error', '❌ No se pudo conectar al servidor');
            }
        } catch (\Exception $e) {
            $this->connectionStatus = 'error';
            session()->flash('error', '❌ Error: ' . $e->getMessage());
        }
    }

    public function saveConfig()
    {
        try {
            // Validate
            if ($this->mode === 'client' && empty($this->serverIp)) {
                session()->flash('error', 'Debes ingresar la IP del servidor');
                return;
            }

            // Guardar en archivo de configuración externo (no .env)
            $this->saveToConfigFile([
                'mode' => $this->mode,
                'server_ip' => $this->serverIp ?? '',
                'server_port' => $this->serverPort ?? 8000,
            ]);
            
            session()->flash('message', '✅ Configuración guardada. Por favor, reinicia la aplicación para aplicar los cambios.');
            
            // NO cerramos el modal para que el usuario vea el mensaje
            // $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    private function saveToConfigFile($data)
    {
        // Guardar en storage/app/pos_config.json (fuera del .exe)
        $configPath = storage_path('app/pos_config.json');
        
        // Asegurar que el directorio existe
        if (!file_exists(dirname($configPath))) {
            mkdir(dirname($configPath), 0755, true);
        }
        
        file_put_contents($configPath, json_encode($data, JSON_PRETTY_PRINT));
    }

    private function getLocalIp()
    {
        try {
            $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            socket_connect($socket, '8.8.8.8', 53);
            socket_getsockname($socket, $localIp);
            socket_close($socket);
            return $localIp;
        } catch (\Exception $e) {
            return '127.0.0.1';
        }
    }

    public function render()
    {
        return view('livewire.network-config');
    }
}
