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
        // Add synced_at field to users table
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('synced_at')->nullable()->after('updated_at');
        });

        // Add synced_at field to products table
        Schema::table('products', function (Blueprint $table) {
            $table->timestamp('synced_at')->nullable()->after('updated_at');
        });

        // Add synced_at field to sales table
        Schema::table('sales', function (Blueprint $table) {
            $table->timestamp('synced_at')->nullable()->after('updated_at');
        });

        // Add synced_at field to clients table
        Schema::table('clients', function (Blueprint $table) {
            $table->timestamp('synced_at')->nullable()->after('updated_at');
        });

        // Add synced_at field to cash_register_sessions table
        Schema::table('cash_register_sessions', function (Blueprint $table) {
            $table->timestamp('synced_at')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('synced_at');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('synced_at');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('synced_at');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('synced_at');
        });

        Schema::table('cash_register_sessions', function (Blueprint $table) {
            $table->dropColumn('synced_at');
        });
    }
};
