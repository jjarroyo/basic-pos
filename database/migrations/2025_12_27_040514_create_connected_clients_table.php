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
        Schema::create('connected_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('connection_id')->unique();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('client_name')->nullable();
            $table->timestamp('connected_at');
            $table->timestamp('last_heartbeat_at');
            $table->timestamp('disconnected_at')->nullable();
            $table->timestamps();
            
            $table->index('connection_id');
            $table->index('user_id');
            $table->index('last_heartbeat_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('connected_clients');
    }
};
