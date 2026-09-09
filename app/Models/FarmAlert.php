<?php
// app/Models/FarmAlert.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmAlert extends Model
{
    protected $fillable = [
        'farm_id','season_id','type','title','message','priority',
        'is_read','is_dismissed','requires_action','action_url',
        'dismissed_at','dismissed_by',
    ];

    protected $casts = [
        'is_read'        => 'boolean',
        'is_dismissed'   => 'boolean',
        'requires_action'=> 'boolean',
        'dismissed_at'   => 'datetime',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(FarmSeason::class, 'season_id');
    }

    public function dismisser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dismissed_by');
    }

    public static function types(): array
    {
        return [
            'cost_anomaly'       => ['label' => 'Cost Anomaly', 'icon' => 'fas fa-chart-line', 'color' => '#f59e0b'],
            'low_yield'          => ['label' => 'Low Yield Warning', 'icon' => 'fas fa-arrow-down', 'color' => '#ef4444'],
            'missed_planting'    => ['label' => 'Missed Planting Window', 'icon' => 'fas fa-calendar-times', 'color' => '#f97316'],
            'weather_risk'       => ['label' => 'Weather Risk', 'icon' => 'fas fa-cloud-showers-heavy', 'color' => '#0ea5e9'],
            'disease_outbreak'   => ['label' => 'Disease Outbreak', 'icon' => 'fas fa-biohazard', 'color' => '#ef4444'],
            'deadline'           => ['label' => 'Deadline Approaching', 'icon' => 'fas fa-clock', 'color' => '#8b5cf6'],
            'recommendation'     => ['label' => 'Recommendation', 'icon' => 'fas fa-lightbulb', 'color' => '#22c55e'],
            'admin_intervention' => ['label' => 'Admin Intervention', 'icon' => 'fas fa-user-shield', 'color' => '#dc2626'],
        ];
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'urgent' => '#dc2626',
            'high'   => '#ef4444',
            'medium' => '#f59e0b',
            'low'    => '#22c55e',
            default  => '#94a3b8',
        };
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false)->where('is_dismissed', false);
    }

    public function scopeActive($query)
    {
        return $query->where('is_dismissed', false);
    }
}
