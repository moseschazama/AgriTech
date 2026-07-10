<?php
// app/Models/Enrollment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id','course_id','status','amount_paid','payment_reference',
        'completed_at','certificate_number','certificate_issued_at',
    ];

    protected $casts = [
        'amount_paid'           => 'decimal:2',
        'completed_at'          => 'datetime',
        'certificate_issued_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * Progress percentage through the course (0–100).
     */
    public function progressPercentage(): float
    {
        $totalLessons = $this->course->publishedLessons()->count();
        if ($totalLessons === 0) return 0;

        $completedLessons = $this->lessonProgress()
                                  ->where('is_completed', true)
                                  ->count();

        return round(($completedLessons / $totalLessons) * 100, 1);
    }

    /**
     * Mark a lesson complete and check if the whole course is now finished.
     * Issues a certificate automatically when 100% complete.
     */
    public function completeLesson(Lesson $lesson): void
    {
        $this->lessonProgress()->updateOrCreate(
            ['user_id' => $this->user_id, 'lesson_id' => $lesson->id],
            ['is_completed' => true, 'completed_at' => now(), 'last_accessed_at' => now()]
        );

        if ($this->progressPercentage() >= 100 && $this->status !== 'completed') {
            $this->markCompleted();
        }
    }

    /**
     * Mark the enrollment as completed and issue a certificate.
     */
    public function markCompleted(): void
    {
        $certNumber = $this->course->has_certificate
            ? 'CERT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6))
            : null;

        $this->update([
            'status'                => 'completed',
            'completed_at'          => now(),
            'certificate_number'    => $certNumber,
            'certificate_issued_at' => $certNumber ? now() : null,
        ]);

        Notification::create([
            'user_id'    => $this->user_id,
            'title'      => '🎉 Course Completed!',
            'message'    => "Congratulations! You completed \"{$this->course->title}\"" .
                             ($certNumber ? " and earned a certificate." : "."),
            'type'       => 'lesson',
            'icon'       => 'fas fa-trophy',
            'icon_color' => 'var(--earth-500)',
        ]);
    }
}
