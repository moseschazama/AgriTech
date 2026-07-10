<?php
// app/Models/CourseGuide.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

/**
 * CourseGuide — an admin-uploaded PDF resource that farmers can download.
 * Distinct from a Course's video Lessons — these are downloadable reference
 * material (field guides, handbooks) tagged by topic so they can be filtered
 * the same way courses are.
 */
class CourseGuide extends Model
{
    protected $fillable = [
        "uploaded_by",
        "course_id",
        "title",
        "description",
        "topic",
        "file_path",
        "file_name",
        "file_size",
        "page_count",
        "download_count",
        "is_published",
    ];

    protected $casts = [
        "is_published" => "boolean",
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, "uploaded_by");
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where("is_published", true);
    }

    public function scopeTopic(Builder $query, string $topic): Builder
    {
        return $query->where("topic", $topic);
    }

    // ── Accessors ────────────────────────────────────────────────────

    /** Human-readable file size, e.g. "2.4 MB" */
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;

        return match (true) {
            $bytes >= 1048576 => round($bytes / 1048576, 1) . " MB",
            $bytes >= 1024 => round($bytes / 1024, 1) . " KB",
            default => $bytes . " B",
        };
    }

    /** Public-facing emoji icon based on topic, used on the cards */
    public function getTopicEmojiAttribute(): string
    {
        return match ($this->topic) {
            "soil_crops" => "🌽",
            "livestock" => "🐄",
            "agri_tech" => "🚁",
            "agribusiness" => "📊",
            "organic" => "🥦",
            "irrigation" => "💧",
            "post_harvest" => "🌾",
            "disease_control" => "🔬",
            default => "📄",
        };
    }

    // ── Business Logic ───────────────────────────────────────────────

    /**
     * Record a download and return the storage path for streaming.
     * Call this from GuideController::download() right before returning
     * the file response.
     */
    public function recordDownload(): string
    {
        $this->increment("download_count");

        return $this->file_path;
    }

    /**
     * Delete both the database record and the underlying file on disk.
     * Always use this instead of ->delete() directly so the file isn't
     * orphaned in storage.
     */
    public function deleteWithFile(): bool
    {
        Storage::disk("public")->delete($this->file_path);

        return $this->delete();
    }
}
