<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Datos de la Empresa
            'company_name' => 'Nexus POS Store',
            'company_nit' => '900.123.456-7',
            'company_address' => 'Calle 123 # 45-67, Bogotá',
            'company_phone' => '300 123 4567',
            'company_email' => 'contacto@nexuspos.com',
            'ticket_footer' => '¡Gracias por su compra! Vuelva pronto.',
            'tax_rate' => '19',
            'currency_symbol' => '$',
        ];

        foreach ($settings as $key => $value) {
            Setting::create(['key' => $key, 'value' => $value]);
        }
    }
}
