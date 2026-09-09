<?php

namespace App\Events;

use App\Models\Delivery;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Delivery $delivery,
        public string $newStatus,
    ) {}

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel("admin.dashboard"),
        ];

        if ($this->delivery->order?->buyer_id) {
            $channels[] = new PrivateChannel("user.{$this->delivery->order->buyer_id}");
        }

        if ($this->delivery->driver?->user_id) {
            $channels[] = new PrivateChannel("user.{$this->delivery->driver->user_id}");
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'delivery.status_updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->delivery->id,
            'tracking_number' => $this->delivery->tracking_number,
            'order_id' => $this->delivery->order_id,
            'order_number' => $this->delivery->order?->order_number,
            'new_status' => $this->newStatus,
            'driver_lat' => $this->delivery->driver_current_lat,
            'driver_lng' => $this->delivery->driver_current_lng,
            'distance_remaining_km' => $this->delivery->distance_remaining_km,
            'updated_at' => now()->toISOString(),
        ];
    }
}
