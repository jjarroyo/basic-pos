<?php

namespace Database\Seeders;

use App\Models\CashRegister;
use Illuminate\Database\Seeder;

class CashRegisterSeeder extends Seeder
{
    public function run(): void
    {
        $cajas = [
            'Caja 1 - Principal',
            'Caja 2 - Mostrador',
            'Caja 3 - Piso',
            'Caja 4 - VIP',
            'Caja 5 - Extra',
        ];

        foreach ($cajas as $nombre) {
            CashRegister::create([
                'name' => $nombre,
                'is_active' => true,
                'is_open' => false,
            ]);
        }
    }
}