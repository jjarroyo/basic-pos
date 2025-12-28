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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_register_session_id')->nullable()->constrained();
            $table->foreignId('user_id')->constrained();
            $table->enum('category', [
                'damaged_products',
                'services',
                'supplies',
                'salaries',
                'rent',
                'other'
            ])->default('other');
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->string('reference_type')->nullable(); // 'return', 'manual'
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('payment_method')->default('cash'); // cash, card, transfer
            $table->string('receipt_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
