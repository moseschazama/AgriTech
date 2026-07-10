<?php
// app/Models/CourseReview.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseReview extends Model
{
    protected $fillable = [
        'user_id','course_id','enrollment_id','rating','review','is_approved',
    ];

    protected $casts = ['is_approved' => 'boolean'];

    protected static function boot()
    {
        parent::boot();
        // Recalculate course rating whenever a review is saved/deleted
        static::saved(fn (CourseReview $r) => $r->course?->recalculateRating());
        static::deleted(fn (CourseReview $r) => $r->course?->recalculateRating());
    }

    public function user(): BelongsTo       { return $this->belongsTo(User::class); }
    public function course(): BelongsTo     { return $this->belongsTo(Course::class); }
    public function enrollment(): BelongsTo { return $this->belongsTo(Enrollment::class); }
}
