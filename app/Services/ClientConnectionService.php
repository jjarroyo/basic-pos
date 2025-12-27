<?php

namespace App\Services;

use App\Models\ConnectedClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClientConnectionService
{
    /**
     * Register a new client connection
     */
    public function registerConnection(?int $userId, Request $request, ?string $clientName = null): ConnectedClient
    {
        $connectionId = Str::uuid()->toString();

        $connection = ConnectedClient::create([
            'user_id' => $userId,
            'connection_id' => $connectionId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'client_name' => $clientName ?? 'Cliente ' . ($userId ? "Usuario #$userId" : 'Anónimo'),
            'connected_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        Log::info('🔌 Cliente conectado', [
            'connection_id' => $connectionId,
            'user_id' => $userId,
            'ip' => $request->ip(),
        ]);

        return $connection;
    }

    /**
     * Update heartbeat for a connection
     */
    public function heartbeat(string $connectionId): bool
    {
        $connection = ConnectedClient::where('connection_id', $connectionId)
            ->whereNull('disconnected_at')
            ->first();

        if (!$connection) {
            return false;
        }

        $connection->update(['last_heartbeat_at' => now()]);

        return true;
    }

    /**
     * Disconnect a client
     */
    public function disconnect(string $connectionId): bool
    {
        $connection = ConnectedClient::where('connection_id', $connectionId)
            ->whereNull('disconnected_at')
            ->first();

        if (!$connection) {
            return false;
        }

        $connection->update(['disconnected_at' => now()]);

        Log::info('🔌 Cliente desconectado', [
            'connection_id' => $connectionId,
            'user_id' => $connection->user_id,
        ]);

        return true;
    }

    /**
     * Get all connected clients
     */
    public function getConnectedClients()
    {
        return ConnectedClient::with('user')
            ->active()
            ->orderBy('connected_at', 'desc')
            ->get();
    }

    /**
     * Clean stale connections (no heartbeat in X minutes)
     */
    public function cleanStaleConnections(int $timeoutMinutes = 5): int
    {
        $staleConnections = ConnectedClient::whereNull('disconnected_at')
            ->where('last_heartbeat_at', '<', now()->subMinutes($timeoutMinutes))
            ->get();

        $count = 0;
        foreach ($staleConnections as $connection) {
            $connection->update(['disconnected_at' => now()]);
            $count++;
        }

        if ($count > 0) {
            Log::info("🧹 Limpieza de conexiones obsoletas: $count conexiones cerradas");
        }

        return $count;
    }

    /**
     * Get connection statistics
     */
    public function getStatistics(): array
    {
        $activeConnections = ConnectedClient::active()->count();
        $totalConnections = ConnectedClient::count();
        $connectionsToday = ConnectedClient::whereDate('connected_at', today())->count();

        return [
            'active' => $activeConnections,
            'total' => $totalConnections,
            'today' => $connectionsToday,
        ];
    }
}
