<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CashRegisterStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $cashRegisterId;
    public bool $isOpen;
    public ?int $userId;
    public ?int $sessionId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $cashRegisterId, bool $isOpen, ?int $userId = null, ?int $sessionId = null)
    {
        $this->cashRegisterId = $cashRegisterId;
        $this->isOpen = $isOpen;
        $this->userId = $userId;
        $this->sessionId = $sessionId;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('cash-registers');
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'cash_register_id' => $this->cashRegisterId,
            'is_open' => $this->isOpen,
            'user_id' => $this->userId,
            'session_id' => $this->sessionId,
            'timestamp' => now()->toISOString(),
        ];
    }
}
