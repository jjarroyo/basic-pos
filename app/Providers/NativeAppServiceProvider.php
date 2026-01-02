<?php

namespace App\Providers;

use Native\Desktop\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Illuminate\Support\Facades\Log;

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

        Log::info('Native App Service Provider Booted.');

        try {
            Log::info('Native App Service Provider Booted.');
            if (\App\Models\Setting::get('backup_enabled') && \App\Models\Setting::get('backup_frequency') === 'startup') {
               
                Log::info('Startup Backup Process Started.');
                $phpBinary = PHP_BINARY;
                $artisan = base_path('artisan');
                
                $process = new \Symfony\Component\Process\Process([$phpBinary, $artisan, 'app:backup-internal']);
                
                $process->run();
                
            }
        } catch (\Throwable $e) {
            Log::error('Startup Backup Process Failed: ' . $e->getMessage());
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
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {        
        return [
              
        ];
    }
}
