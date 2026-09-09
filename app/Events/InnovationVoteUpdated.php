<?php

namespace App\Events;

use App\Models\Innovation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InnovationVoteUpdated implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Innovation $innovation,
        public bool $voted,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('innovation.hub'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'innovation.vote_updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->innovation->id,
            'slug' => $this->innovation->slug,
            'vote_count' => $this->innovation->vote_count,
            'voted' => $this->voted,
        ];
    }
}
