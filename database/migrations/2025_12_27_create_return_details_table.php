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
        Schema::create('return_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained()->onDelete('cascade');
            $table->foreignId('sale_detail_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->enum('disposition', [
                'return_to_stock',
                'exchange',
                'damaged_with_expense',
                'damaged_no_expense'
            ]);
            $table->text('disposition_notes')->nullable();
            
            // Exchange fields
            $table->foreignId('exchange_product_id')->nullable()->constrained('products');
            $table->integer('exchange_quantity')->nullable();
            $table->decimal('exchange_unit_price', 10, 2)->nullable();
            $table->decimal('price_difference', 10, 2)->nullable()->comment('Positive=customer pays, Negative=refund');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_details');
    }
};
