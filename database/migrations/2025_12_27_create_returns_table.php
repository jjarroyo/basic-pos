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
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('cash_register_session_id')->constrained();
            $table->decimal('total_refund', 10, 2)->default(0);
            $table->string('payment_method'); // cash, card (from original sale)
            $table->text('reason');
            $table->text('notes')->nullable();
            $table->string('status')->default('completed'); // completed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
