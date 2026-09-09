<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
        public string $oldStatus,
    ) {}

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel("user.{$this->order->buyer_id}"),
            new PrivateChannel("admin.dashboard"),
        ];

        if ($this->order->seller_id) {
            $channels[] = new PrivateChannel("user.{$this->order->seller_id}");
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'order.status_changed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->order->status,
            'total' => $this->order->total,
            'updated_at' => now()->toISOString(),
        ];
    }
}
