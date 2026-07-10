<?php
// app/Models/Lesson.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    protected $fillable = [
        'course_id','title','description','sort_order','type',
        'video_url','pdf_path','duration_minutes','is_free_preview','is_published',
    ];

    protected $casts = [
        'is_free_preview' => 'boolean',
        'is_published'    => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        // Keep parent course stats in sync
        static::saved(fn (Lesson $l) => $l->course?->recalculateStats());
        static::deleted(fn (Lesson $l) => $l->course?->recalculateStats());
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * Can the given user access this lesson?
     * Free preview lessons are always accessible; otherwise requires enrollment.
     */
    public function isAccessibleBy(?User $user): bool
    {
        if ($this->is_free_preview) return true;
        if (!$user) return false;

        return $this->course->enrollments()
                    ->where('user_id', $user->id)
                    ->where('status', 'active')
                    ->exists();
    }
}
