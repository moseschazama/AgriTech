<?php
// app/Models/Course.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'instructor_id','title','slug','description','what_you_learn',
        'category','level','status','access_type','price','currency',
        'thumbnail','preview_video','is_featured','has_certificate','works_offline',
    ];

    protected $casts = [
        'price'              => 'decimal:2',
        'average_rating'     => 'decimal:2',
        'is_featured'        => 'boolean',
        'has_certificate'    => 'boolean',
        'works_offline'      => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        // Auto-generate slug from title on creation
        static::creating(function (Course $course) {
            $course->slug = $course->slug ?: Str::slug($course->title) . '-' . Str::random(5);
        });
    }

    // ── Relationships ─────────────────────────────────────────────────

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }

    public function publishedLessons(): HasMany
    {
        return $this->lessons()->where('is_published', true);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')
                    ->withPivot('status','completed_at')
                    ->withTimestamps();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CourseReview::class)->where('is_approved', true);
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFree($query)
    {
        return $query->where('access_type', 'free');
    }

    public function scopePremium($query)
    {
        return $query->where('access_type', 'premium');
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getIsFreeAttribute(): bool
    {
        return $this->access_type === 'free' || $this->price == 0;
    }

    public function getFormattedDurationAttribute(): string
    {
        $h = intdiv($this->total_duration_minutes, 60);
        $m = $this->total_duration_minutes % 60;
        return "{$h}h {$m}m";
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }

    // ── Business Logic ────────────────────────────────────────────────

    /**
     * Enroll a user in this course. Handles free vs paid logic.
     *
     * @throws \Exception if already enrolled or payment required but not provided
     */
    public function enroll(User $user, ?string $paymentReference = null): Enrollment
    {
        if ($this->enrollments()->where('user_id', $user->id)->exists()) {
            throw new \Exception('User is already enrolled in this course.');
        }

        if (!$this->is_free && !$paymentReference) {
            throw new \Exception('Payment is required for this premium course.');
        }

        $enrollment = $this->enrollments()->create([
            'user_id'            => $user->id,
            'status'             => 'active',
            'amount_paid'        => $this->is_free ? 0 : $this->price,
            'payment_reference'  => $paymentReference,
        ]);

        $this->increment('total_enrolled');
        $user->increment('total_courses_enrolled');

        // Notify the user
        Notification::create([
            'user_id'    => $user->id,
            'title'      => 'Enrolled Successfully!',
            'message'    => "You're now enrolled in \"{$this->title}\". Start learning today.",
            'type'       => 'lesson',
            'icon'       => 'fas fa-graduation-cap',
            'icon_color' => 'var(--primary)',
            'action_url' => "/learn/{$this->slug}",
        ]);

        return $enrollment;
    }

    /**
     * Recalculate and persist the average rating.
     */
    public function recalculateRating(): void
    {
        $this->update([
            'average_rating' => round($this->reviews()->avg('rating') ?? 0, 2),
            'total_reviews'  => $this->reviews()->count(),
        ]);
    }

    /**
     * Recalculate total lessons + duration after lesson changes.
     */
    public function recalculateStats(): void
    {
        $this->update([
            'total_lessons'          => $this->publishedLessons()->count(),
            'total_duration_minutes' => $this->publishedLessons()->sum('duration_minutes'),
        ]);
    }
}
