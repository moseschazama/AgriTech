<?php

namespace App\Events;

use App\Models\LessonProgress;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LessonCompleted implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public LessonProgress $progress,
        public int $courseProgress,
        public bool $courseDone,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("user.{$this->progress->user_id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'lesson.completed';
    }

    public function broadcastWith(): array
    {
        return [
            'lesson_id' => $this->progress->lesson_id,
            'lesson_title' => $this->progress->lesson?->title ?? 'Unknown',
            'course_progress' => $this->courseProgress,
            'course_completed' => $this->courseDone,
            'completed_at' => $this->progress->completed_at?->toISOString(),
        ];
    }
}
