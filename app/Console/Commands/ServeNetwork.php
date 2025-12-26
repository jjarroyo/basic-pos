<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ServeNetwork extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start PHP server accessible from network (server mode)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (config('pos.mode') !== 'server') {
            $this->error('This command only works in server mode.');
            $this->info('Configure the app as "Server" in network settings first.');
            return 1;
        }

       $this->info('Starting network server on 0.0.0.0:8000...');
        
        $basePath = base_path();
        $publicPath = public_path();

        // Agregar la variable de entorno aquí también (Windows sintaxis)
        passthru(sprintf(
            'set NATIVE_SERVER_MODE=true && php -S 0.0.0.0:8000 -t "%s" "%s"',
            $publicPath,
            $basePath . '/server.php'
        ));

        return 0;
    }
}
