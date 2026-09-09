<?php

namespace App\Events;

use App\Models\Enrollment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseCompleted implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Enrollment $enrollment,
        public int $progress,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("user.{$this->enrollment->user_id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'course.completed';
    }

    public function broadcastWith(): array
    {
        return [
            'enrollment_id' => $this->enrollment->id,
            'course_id' => $this->enrollment->course_id,
            'course_title' => $this->enrollment->course?->title ?? 'Unknown',
            'certificate_number' => $this->enrollment->certificate_number,
            'progress' => $this->progress,
            'completed_at' => $this->enrollment->completed_at?->toISOString(),
        ];
    }
}
