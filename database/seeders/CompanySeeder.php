<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Company::create([
            // Información básica de la empresa
            'name' => 'MI EMPRESA S.A.S.', // Cambiar por tu razón social
            'trade_name' => 'Mi Empresa', // Cambiar por tu nombre comercial
            'nit' => '900123456', // Cambiar por tu NIT (sin dígito de verificación)
            'dv' => '3', // Cambiar por tu dígito de verificación
            
            // Régimen y responsabilidades fiscales
            'regime_type' => 'comun', // 'comun' o 'simplificado'
            'responsibility_codes' => [
                'O-13', // Gran contribuyente
                'O-15', // Autorretenedor
                'O-47', // Régimen simple de tributación
                // Otros códigos según aplique:
                // 'O-23' => Agente de retención IVA
                // 'R-99-PN' => Responsabilidades del régimen común
            ],
            
            // Información de contacto
            'email' => 'facturacion@miempresa.com', // Email para facturación
            'phone' => '+57 601 1234567', // Teléfono con indicativo
            'website' => 'https://www.miempresa.com', // Sitio web (opcional)
            
            // Dirección fiscal
            'address' => 'Calle 123 # 45-67 Oficina 801', // Dirección completa
            'city' => 'Bogotá D.C.', // Ciudad
            'city_code' => '11001', // Código DANE de Bogotá
            'department' => 'Cundinamarca', // Departamento
            'department_code' => '11', // Código DANE de Cundinamarca
            'country' => 'CO', // Código ISO del país
            'postal_code' => '110111', // Código postal
            
            // Información comercial y legal
            'economic_activity_code' => '4711', // Código CIIU (ejemplo: comercio al por menor)
            'economic_activity_description' => 'Comercio al por menor en establecimientos no especializados',
            'merchant_registration' => '12345678', // Número de matrícula mercantil
            'merchant_registration_date' => '2020-01-15', // Fecha de matrícula
            
            // Logo (opcional, puedes agregarlo después)
            'logo_path' => null, // Ruta al logo: 'logos/company-logo.png'
            
            // Nota al pie de factura (opcional)
            'invoice_footer_note' => 'Gracias por su compra. Esta factura electrónica fue generada por un sistema autorizado por la DIAN.',
            
            // Estado
            'is_active' => true,
        ]);
    }
}
