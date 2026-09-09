<?php
// app/Models/FarmSeason.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmSeason extends Model
{
    protected $fillable = [
        'farm_id','name','crop','status','field_size_hectares','field_name',
        'planted_at','expected_harvest_at','harvested_at',
        'expected_yield_kg','actual_yield_kg','yield_per_hectare',
        'expected_revenue','actual_revenue','currency',
        'total_costs','net_profit','roi_percentage','notes',
    ];

    protected $casts = [
        'field_size_hectares' => 'decimal:2',
        'expected_yield_kg'   => 'decimal:2',
        'actual_yield_kg'     => 'decimal:2',
        'yield_per_hectare'   => 'decimal:2',
        'expected_revenue'    => 'decimal:2',
        'actual_revenue'      => 'decimal:2',
        'total_costs'         => 'decimal:2',
        'net_profit'          => 'decimal:2',
        'roi_percentage'      => 'decimal:2',
        'planted_at'          => 'date',
        'expected_harvest_at' => 'date',
        'harvested_at'        => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────────────
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function costs(): HasMany
    {
        return $this->hasMany(FarmCost::class, 'season_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(FarmSale::class, 'season_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(FarmEvent::class, 'season_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(FarmAlert::class, 'season_id');
    }

    // ── Business Logic ────────────────────────────────────────────────
    public function recalculateTotals(): void
    {
        $totalCosts = $this->costs()->sum('total_cost');
        $totalRevenue = $this->sales()->sum('total_revenue');
        $netProfit = $totalRevenue - $totalCosts;
        $roi = $totalCosts > 0 ? round(($netProfit / $totalCosts) * 100, 2) : null;

        $this->update([
            'total_costs'    => $totalCosts,
            'actual_revenue' => $totalRevenue,
            'net_profit'     => $netProfit,
            'roi_percentage' => $roi,
        ]);
    }

    public function getDaysUntilHarvestAttribute(): ?int
    {
        if (!$this->expected_harvest_at || $this->status === 'completed') return null;
        return (int) now()->diffInDays($this->expected_harvest_at, false);
    }

    public function getProgressPercentageAttribute(): int
    {
        if (!$this->planted_at || !$this->expected_harvest_at) return 0;
        $total = $this->planted_at->diffInDays($this->expected_harvest_at);
        $elapsed = $this->planted_at->diffInDays(now());
        return min(100, max(0, (int) (($elapsed / $total) * 100)));
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCurrentSeason($query)
    {
        return $query->where('status', 'active')
                    ->orWhere('status', 'planning');
    }
}
