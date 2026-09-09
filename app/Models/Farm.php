<?php
// app/Models/Farm.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Farm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id','name','size_hectares','farm_type','district','region',
        'village','latitude','longitude','primary_crops','livestock',
        'irrigation_type','has_storage','has_greenhouse','is_organic_certified',
        'is_verified','verified_at','verified_by','description',
    ];

    protected $casts = [
        'primary_crops'         => 'array',
        'livestock'             => 'array',
        'size_hectares'         => 'decimal:2',
        'has_storage'           => 'boolean',
        'has_greenhouse'        => 'boolean',
        'is_organic_certified'  => 'boolean',
        'is_verified'           => 'boolean',
        'verified_at'           => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function productions(): HasMany
    {
        return $this->hasMany(FarmProduction::class);
    }

    public function seasons(): HasMany
    {
        return $this->hasMany(FarmSeason::class);
    }

    public function costs(): HasManyThrough
    {
        return $this->hasManyThrough(FarmCost::class, FarmSeason::class, 'farm_id', 'season_id');
    }

    public function sales(): HasManyThrough
    {
        return $this->hasManyThrough(FarmSale::class, FarmSeason::class, 'farm_id', 'season_id');
    }

    public function events(): HasManyThrough
    {
        return $this->hasManyThrough(FarmEvent::class, FarmSeason::class, 'farm_id', 'season_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(FarmAlert::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ── Business Logic ────────────────────────────────────────────────

    /**
     * Total revenue across all recorded seasons.
     */
    public function totalRevenue(): float
    {
        return $this->productions()->sum('revenue');
    }

    /**
     * Average yield per hectare across all seasons for a given crop.
     */
    public function averageYieldFor(string $crop): ?float
    {
        return $this->productions()
                    ->where('crop', $crop)
                    ->avg('yield_per_hectare');
    }

    /**
     * Get the most recent production record.
     */
    public function latestProduction(): ?FarmProduction
    {
        return $this->productions()->latest('harvested_at')->first();
    }

    /**
     * Verify the farm (admin action).
     */
    public function verify(User $admin): void
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $admin->id,
        ]);
    }
}
