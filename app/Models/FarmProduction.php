<?php
// app/Models/FarmProduction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmProduction extends Model
{
    protected $fillable = [
        'farm_id','season','crop','yield_per_hectare','total_yield',
        'revenue','currency','planted_at','harvested_at','notes',
    ];

    protected $casts = [
        'yield_per_hectare' => 'decimal:2',
        'total_yield'       => 'decimal:2',
        'revenue'           => 'decimal:2',
        'planted_at'        => 'date',
        'harvested_at'      => 'date',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /** Days between planting and harvest. */
    public function getGrowthDurationDaysAttribute(): ?int
    {
        if (!$this->planted_at || !$this->harvested_at) return null;
        return $this->planted_at->diffInDays($this->harvested_at);
    }
}
