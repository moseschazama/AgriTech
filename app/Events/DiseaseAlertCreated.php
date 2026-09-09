<?php

namespace App\Events;

use App\Models\DiseaseAlert;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiseaseAlertCreated implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public DiseaseAlert $alert) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.dashboard'),
            new PrivateChannel("disease.alerts"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'disease.alert_created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->alert->id,
            'title' => $this->alert->title,
            'description' => $this->alert->description,
            'alert_type' => $this->alert->alert_type,
            'affected_districts' => $this->alert->affected_districts,
            'affected_crops' => $this->alert->affected_crops,
            'recommended_action' => $this->alert->recommended_action,
            'created_at' => $this->alert->created_at->toISOString(),
        ];
    }
}
