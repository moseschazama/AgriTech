<?php
// app/Models/DiseaseDetection.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiseaseDetection extends Model
{
    protected $fillable = [
        'user_id','disease_id','image_path','detected_disease','affected_crop',
        'confidence_score','all_predictions','detection_report','severity','status','farm_location',
        'user_confirmed','user_notes',
    ];

    protected $casts = [
        'confidence_score' => 'decimal:2',
        'all_predictions'  => 'array',
        'detection_report' => 'array',
        'user_confirmed'   => 'boolean',
    ];

    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function disease(): BelongsTo { return $this->belongsTo(Disease::class); }

    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }

    /**
     * Persist the full decision-support report for history viewing.
     */
    public function updateDetectionReport(array $report): void
    {
        $this->update([
            'detection_report' => $report,
            'status' => 'processed',
        ]);
    }

    /**
     * Apply diagnosis results to this detection record and link
     * to the matching Disease library entry if one exists.
     */
    public function applyPrediction(array $prediction): void
    {
        $disease = Disease::where('name', $prediction['disease_name'])->first();

        $this->update([
            'disease_id'        => $disease?->id,
            'detected_disease'  => $prediction['disease_name'],
            'affected_crop'     => $prediction['crop'] ?? null,
            'confidence_score'  => $prediction['confidence'],
            'all_predictions'   => $prediction['top_predictions'] ?? [],
            'severity'          => $disease?->severity ?? 'Medium',
            'status'            => 'processed',
        ]);

        Notification::create([
            'user_id'    => $this->user_id,
            'title'      => "Disease Detected: {$this->detected_disease}",
            'message'    => "Diagnosis identified {$this->detected_disease} with " .
                             round($this->confidence_score) . "% confidence. View treatment guide.",
            'type'       => 'disease',
            'icon'       => 'fas fa-bug',
            'icon_color' => 'var(--danger)',
            'action_url' => $disease ? "/diseases#{$disease->slug}" : '/diseases',
        ]);
    }
}
