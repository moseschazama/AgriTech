<?php
// app/Models/FarmCost.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmCost extends Model
{
    protected $fillable = [
        'season_id','category','item_name','quantity','unit',
        'unit_cost','total_cost','currency',
        'purchased_at','supplier','notes',
    ];

    protected $casts = [
        'quantity'    => 'decimal:2',
        'unit_cost'   => 'decimal:2',
        'total_cost'  => 'decimal:2',
        'purchased_at'=> 'date',
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(FarmSeason::class, 'season_id');
    }

    // ── Category Labels ──────────────────────────────────────────────
    public static function categories(): array
    {
        return [
            'seed'        => ['label' => 'Seeds & Planting Material', 'icon' => 'fas fa-seedling', 'color' => '#22c55e'],
            'fertilizer'  => ['label' => 'Fertilizer & Soil Amendments', 'icon' => 'fas fa-flask', 'color' => '#3b82f6'],
            'chemicals'   => ['label' => 'Pesticides & Chemicals', 'icon' => 'fas fa-vial', 'color' => '#ef4444'],
            'labour'      => ['label' => 'Labour Costs', 'icon' => 'fas fa-users', 'color' => '#f59e0b'],
            'transport'   => ['label' => 'Transport & Logistics', 'icon' => 'fas fa-truck', 'color' => '#8b5cf6'],
            'irrigation'  => ['label' => 'Irrigation & Water', 'icon' => 'fas fa-tint', 'color' => '#0ea5e9'],
            'equipment'   => ['label' => 'Equipment & Tools', 'icon' => 'fas fa-tools', 'color' => '#64748b'],
            'rent'        => ['label' => 'Land Rent & Leases', 'icon' => 'fas fa-map', 'color' => '#ec4899'],
            'other'       => ['label' => 'Other Costs', 'icon' => 'fas fa-ellipsis-h', 'color' => '#94a3b8'],
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categories()[$this->category]['label'] ?? $this->category;
    }

    public function getCategoryIconAttribute(): string
    {
        return self::categories()[$this->category]['icon'] ?? 'fas fa-question';
    }

    public function getCategoryColorAttribute(): string
    {
        return self::categories()[$this->category]['color'] ?? '#94a3b8';
    }
}
