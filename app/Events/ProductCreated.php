<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductCreated implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Product $product) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('marketplace'),
            new PrivateChannel("admin.dashboard"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'product.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->product->id,
            'name' => $this->product->name,
            'slug' => $this->product->slug,
            'price' => $this->product->price,
            'unit' => $this->product->unit,
            'category' => $this->product->category,
            'district' => $this->product->district,
            'status' => $this->product->status,
            'thumbnail' => $this->product->thumbnail,
            'seller_name' => $this->product->seller?->full_name ?? 'Unknown',
            'created_at' => $this->product->created_at->toISOString(),
        ];
    }
}
