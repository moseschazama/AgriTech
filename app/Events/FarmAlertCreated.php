<?php

namespace App\Events;

use App\Models\FarmAlert;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FarmAlertCreated implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public FarmAlert $alert) {}

    public function broadcastOn(): array
    {
        $channels = [];

        if ($this->alert->farm?->user_id) {
            $channels[] = new PrivateChannel("user.{$this->alert->farm->user_id}");
        }

        $channels[] = new PrivateChannel('admin.farm-alerts');

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'farm.alert_created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->alert->id,
            'farm_id' => $this->alert->farm_id,
            'type' => $this->alert->type,
            'title' => $this->alert->title,
            'message' => $this->alert->message,
            'priority' => $this->alert->priority,
            'requires_action' => $this->alert->requires_action,
            'action_url' => $this->alert->action_url,
            'created_at' => $this->alert->created_at->toISOString(),
        ];
    }
}
