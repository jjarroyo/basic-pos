<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::create([
            'name' => 'Consumidor Final',
            'identification' => '222222222222', 
            'document_type' => 'CC',  
            'email' => 'consumidor@final.com',
            'address' => 'Ciudad',
            'phone' => '0000000000',
        ]);
    }
}
