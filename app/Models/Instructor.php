<?php
// app/Models/Instructor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instructor extends Model
{
    protected $fillable = [
        'user_id','name','title','specialization','bio',
        'avatar','email','is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /** Display name with title, e.g. "Dr. Mwale" */
    public function getDisplayNameAttribute(): string
    {
        return trim("{$this->title} {$this->name}");
    }

    /** Total students taught across all courses. */
    public function totalStudents(): int
    {
        return $this->courses()->sum('total_enrolled');
    }
}
