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
