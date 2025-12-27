<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductStockUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $productId;
    public int $newStock;
    public string $updatedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(int $productId, int $newStock, string $updatedBy = 'system')
    {
        $this->productId = $productId;
        $this->newStock = $newStock;
        $this->updatedBy = $updatedBy;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('stock-updates');
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'product_id' => $this->productId,
            'new_stock' => $this->newStock,
            'updated_by' => $this->updatedBy,
            'timestamp' => now()->toISOString(),
        ];
    }
}
