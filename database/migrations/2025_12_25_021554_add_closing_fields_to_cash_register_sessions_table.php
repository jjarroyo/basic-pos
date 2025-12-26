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
        Schema::table('cash_register_sessions', function (Blueprint $table) {
            $table->decimal('expected_cash', 10, 2)->nullable()->after('closing_amount');
            $table->decimal('expected_card', 10, 2)->nullable()->after('expected_cash');
            $table->decimal('actual_cash', 10, 2)->nullable()->after('expected_card');
            $table->decimal('difference', 10, 2)->default(0)->after('actual_cash');
            $table->text('closing_notes')->nullable()->after('difference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_register_sessions', function (Blueprint $table) {
            $table->dropColumn(['expected_cash', 'expected_card', 'actual_cash', 'difference', 'closing_notes']);
        });
    }
};
