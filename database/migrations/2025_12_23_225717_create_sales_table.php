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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();  
            $table->foreignId('cash_register_id')->constrained(); 
            $table->foreignId('client_id')->nullable()->constrained();  
            
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->decimal('total', 10, 2);
            
            $table->string('payment_method')->default('cash'); 
            $table->decimal('cash_received', 10, 2)->nullable(); 
            $table->decimal('change', 10, 2)->default(0); 
            
            $table->string('status')->default('completed'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
