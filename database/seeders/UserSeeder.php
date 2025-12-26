<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roleAdmin = Role::create(['name' => 'admin']);
        $roleSeller = Role::create(['name' => 'seller']);

        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@nexus.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole($roleAdmin);
 
        /*for ($i = 1; $i <= 4; $i++) {
            $seller = User::create([
                'name' => "Vendedor $i",
                'email' => "vendedor$i@nexus.com",
                'password' => Hash::make('password'),
            ]);
            $seller->assignRole($roleSeller);
        }*/
    }
}
