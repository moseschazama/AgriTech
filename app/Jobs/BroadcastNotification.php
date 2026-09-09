<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 5;

    public function __construct(
        public int $userId,
        public string $title,
        public string $message,
        public string $type = 'info',
        public string $icon = 'fas fa-bell',
        public string $iconColor = 'var(--primary)',
        public ?string $actionUrl = null,
    ) {}

    public function handle(): void
    {
        $notification = Notification::create([
            'user_id' => $this->userId,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'icon' => $this->icon,
            'icon_color' => $this->iconColor,
            'action_url' => $this->actionUrl,
            'is_read' => false,
        ]);

        event(new \App\Events\NotificationCreated($notification));
    }
}
