<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order) {}

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
        return 'order.placed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'buyer_name' => $this->order->buyer?->full_name ?? 'Unknown',
            'seller_id' => $this->order->seller_id,
            'total' => $this->order->total,
            'currency' => $this->order->currency,
            'status' => $this->order->status,
            'payment_method' => $this->order->payment_method,
            'created_at' => $this->order->created_at->toISOString(),
        ];
    }
}
