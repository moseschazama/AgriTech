<?php
// app/Models/FarmEvent.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmEvent extends Model
{
    protected $fillable = [
        'season_id','type','title','description','occurred_at','severity',
        'temperature_c','rainfall_mm','weather_condition',
        'product_used','dosage','dosage_unit',
        'requires_follow_up','follow_up_date','is_resolved',
    ];

    protected $casts = [
        'occurred_at'    => 'date',
        'temperature_c'  => 'decimal:1',
        'rainfall_mm'    => 'decimal:1',
        'dosage'         => 'decimal:2',
        'follow_up_date' => 'date',
        'requires_follow_up' => 'boolean',
        'is_resolved'        => 'boolean',
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(FarmSeason::class, 'season_id');
    }

    public static function types(): array
    {
        return [
            'activity'   => ['label' => 'Farm Activity', 'icon' => 'fas fa-tractor', 'color' => '#22c55e'],
            'weather'    => ['label' => 'Weather Event', 'icon' => 'fas fa-cloud-sun', 'color' => '#0ea5e9'],
            'disease'    => ['label' => 'Disease Detected', 'icon' => 'fas fa-bug', 'color' => '#ef4444'],
            'pest'       => ['label' => 'Pest Sighting', 'icon' => 'fas fa-spider', 'color' => '#f97316'],
            'treatment'  => ['label' => 'Treatment Applied', 'icon' => 'fas fa-syringe', 'color' => '#8b5cf6'],
            'observation'=> ['label' => 'Observation', 'icon' => 'fas fa-eye', 'color' => '#64748b'],
            'milestone'  => ['label' => 'Milestone Reached', 'icon' => 'fas fa-flag', 'color' => '#eab308'],
            'alert'      => ['label' => 'Alert', 'icon' => 'fas fa-exclamation-triangle', 'color' => '#ef4444'],
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::types()[$this->type]['label'] ?? $this->type;
    }

    public function getTypeIconAttribute(): string
    {
        return self::types()[$this->type]['icon'] ?? 'fas fa-info-circle';
    }

    public function getTypeColorAttribute(): string
    {
        return self::types()[$this->type]['color'] ?? '#64748b';
    }

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false)->where('requires_follow_up', true);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('occurred_at', '>=', now()->subDays($days));
    }
}
