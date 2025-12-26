<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            
            // Información básica de la empresa
            $table->string('name'); // Razón social
            $table->string('trade_name')->nullable(); // Nombre comercial
            $table->string('nit')->unique(); // NIT sin dígito de verificación
            $table->string('dv', 1); // Dígito de verificación
            
            // Régimen y responsabilidades fiscales
            $table->enum('regime_type', ['comun', 'simplificado'])->default('comun'); // Régimen tributario
            $table->json('responsibility_codes')->nullable(); // Códigos de responsabilidad fiscal (O-13, O-15, O-47, etc.)
            
            // Información de contacto
            $table->string('email'); // Email principal
            $table->string('phone')->nullable(); // Teléfono
            $table->string('website')->nullable(); // Sitio web
            
            // Dirección fiscal
            $table->string('address'); // Dirección completa
            $table->string('city'); // Ciudad
            $table->string('city_code')->nullable(); // Código DANE de la ciudad
            $table->string('department'); // Departamento
            $table->string('department_code')->nullable(); // Código DANE del departamento
            $table->string('country')->default('CO'); // País (código ISO)
            $table->string('postal_code')->nullable(); // Código postal
            
            // Información comercial y legal
            $table->string('economic_activity_code')->nullable(); // Código CIIU
            $table->string('economic_activity_description')->nullable(); // Descripción de la actividad
            $table->string('merchant_registration')->nullable(); // Matrícula mercantil
            $table->date('merchant_registration_date')->nullable(); // Fecha de matrícula
            
            // Logo y representación gráfica
            $table->string('logo_path')->nullable(); // Ruta del logo
            
            // Información adicional para facturación
            $table->text('invoice_footer_note')->nullable(); // Nota al pie de factura
            $table->boolean('is_active')->default(true); // Si está activa
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
