<?php

namespace App\Events;

use App\Models\Enrollment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseEnrolled implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Enrollment $enrollment) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("user.{$this->enrollment->user_id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'course.enrolled';
    }

    public function broadcastWith(): array
    {
        return [
            'enrollment_id' => $this->enrollment->id,
            'course_id' => $this->enrollment->course_id,
            'course_title' => $this->enrollment->course?->title ?? 'Unknown Course',
            'user_name' => $this->enrollment->user?->full_name ?? 'Unknown',
            'created_at' => $this->enrollment->created_at->toISOString(),
        ];
    }
}
