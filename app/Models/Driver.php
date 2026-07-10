<?php
// app/Models/Driver.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    protected $fillable = [
        'user_id','license_number','vehicle_type','vehicle_plate','status',
        'current_lat','current_lng','location_updated_at','is_verified',
    ];

    protected $casts = [
        'current_lat'         => 'decimal:7',
        'current_lng'         => 'decimal:7',
        'location_updated_at' => 'datetime',
        'is_verified'         => 'boolean',
        'average_rating'      => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function activeDeliveries(): HasMany
    {
        return $this->deliveries()->whereNotIn('status', ['delivered', 'failed']);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Update the driver's live GPS position. Called every ~30s from mobile app.
     */
    public function updateLocation(float $lat, float $lng): void
    {
        $this->update([
            'current_lat'         => $lat,
            'current_lng'         => $lng,
            'location_updated_at' => now(),
        ]);

        // Push to any active delivery too
        $this->activeDeliveries->each(function (Delivery $delivery) use ($lat, $lng) {
            $delivery->updateDriverLocation($lat, $lng);
        });
    }

    /**
     * Assign this driver to a delivery (must be available).
     */
    public function assignTo(Delivery $delivery): void
    {
        if ($this->status !== 'available') {
            throw new \Exception('Driver is not available for new deliveries.');
        }

        $delivery->update([
            'driver_id'   => $this->id,
            'status'      => 'assigned',
            'assigned_at' => now(),
        ]);

        $this->update(['status' => 'on_delivery']);
    }
}
