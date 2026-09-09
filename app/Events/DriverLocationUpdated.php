<?php

namespace App\Events;

use App\Models\Delivery;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Delivery $delivery,
        public float $lat,
        public float $lng,
    ) {}

    public function broadcastOn(): array
    {
        $channels = [];

        if ($this->delivery->order?->buyer_id) {
            $channels[] = new PrivateChannel("user.{$this->delivery->order->buyer_id}");
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'driver.location_updated';
    }

    public function broadcastWith(): array
    {
        return [
            'delivery_id' => $this->delivery->id,
            'tracking_number' => $this->delivery->tracking_number,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'distance_remaining_km' => $this->delivery->distance_remaining_km,
            'updated_at' => now()->toISOString(),
        ];
    }
}
