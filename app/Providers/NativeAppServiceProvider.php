<?php

namespace App\Providers;

use Native\Desktop\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Artisan::call('migrate', ['--force' => true]);
 
        if (User::count() === 0) { 
            Artisan::call('db:seed', ['--force' => true]);
        }
        
        if (config('pos.mode') === 'server' && php_sapi_name() !== 'cli-server') {
            $this->startNetworkServer();
        }
    
        Window::open()
            ->width(1280)
            ->height(900)
            ->minWidth(800)
            ->minHeight(600)
            ->title('Nexus POS')
            ->rememberState();
    }
    
    /**
     * Start PHP server accessible from network (server mode only)
     */
    protected function startNetworkServer(): void
    {
        $basePath = base_path();
        $publicPath = public_path();
        
        // CORRECCIÓN 2: Agregar variable de entorno NATIVE_SERVER_MODE=true
        // Esto servirá para identificar este proceso y desactivar los Queue Workers
        $batchFile = $basePath . '/start-server.bat';
        $batchContent = sprintf(
            "@echo off\nset NATIVE_SERVER_MODE=true\nphp -S 0.0.0.0:8000 -t \"%s\" \"%s\"",
            $publicPath,
            $basePath . '/server.php'
        );
        
        file_put_contents($batchFile, $batchContent);
        
        // Script VBS para ejecución silenciosa (sin ventana negra y sin bloqueo)
        $vbsFile = $basePath . '/start-server.vbs';
        $vbsContent = sprintf(
            'Set WshShell = CreateObject("WScript.Shell")' . "\n" .
            'WshShell.Run """%s""", 0, False',
            $batchFile
        );
        
        file_put_contents($vbsFile, $vbsContent);
        
        // Ejecutar
        exec('wscript.exe "' . $vbsFile . '"');
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {        
        return [
              
        ];
    }
}
