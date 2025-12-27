<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConnectedClient extends Model
{
    protected $fillable = [
        'user_id',
        'connection_id',
        'ip_address',
        'user_agent',
        'client_name',
        'connected_at',
        'last_heartbeat_at',
        'disconnected_at',
    ];

    protected $casts = [
        'connected_at' => 'datetime',
        'last_heartbeat_at' => 'datetime',
        'disconnected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only active connections
     */
    public function scopeActive($query)
    {
        return $query->whereNull('disconnected_at')
            ->where('last_heartbeat_at', '>=', now()->subMinutes(5));
    }
}
