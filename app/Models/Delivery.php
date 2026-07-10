<?php
// app/Models/Delivery.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delivery extends Model
{
    protected $fillable = [
        'tracking_number','order_id','driver_id','status',
        'origin_address','origin_district','origin_region','origin_lat','origin_lng',
        'destination_address','destination_district','destination_region','destination_lat','destination_lng',
        'driver_current_lat','driver_current_lng','distance_remaining_km',
        'driver_location_updated_at','assigned_at','collected_at',
        'estimated_arrival_at','delivered_at','delivery_notes',
        'proof_of_delivery','failure_reason','buyer_rating',
    ];

    protected $casts = [
        'origin_lat'                  => 'decimal:7',
        'origin_lng'                  => 'decimal:7',
        'destination_lat'             => 'decimal:7',
        'destination_lng'             => 'decimal:7',
        'driver_current_lat'          => 'decimal:7',
        'driver_current_lng'          => 'decimal:7',
        'driver_location_updated_at'  => 'datetime',
        'assigned_at'                 => 'datetime',
        'collected_at'                => 'datetime',
        'estimated_arrival_at'        => 'datetime',
        'delivered_at'                => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Delivery $d) {
            $d->tracking_number = $d->tracking_number
                ?: 'TRK-' . now()->format('Ymd') . '-' . str_pad(
                    (static::whereDate('created_at', today())->count()) + 1, 3, '0', STR_PAD_LEFT
                );
        });
    }

    // ── Relationships ─────────────────────────────────────────────────

    public function order(): BelongsTo  { return $this->belongsTo(Order::class); }
    public function driver(): BelongsTo { return $this->belongsTo(Driver::class); }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(DeliveryStatusLog::class)->orderByDesc('created_at');
    }

    // ── GPS / Tracking Logic ──────────────────────────────────────────

    /**
     * Update the live position of the assigned driver, recompute remaining
     * distance and ETA, and flag "near_destination" within 2km.
     */
    public function updateDriverLocation(float $lat, float $lng): void
    {
        $distanceKm = $this->haversineDistance(
            $lat, $lng, (float) $this->destination_lat, (float) $this->destination_lng
        );

        $updates = [
            'driver_current_lat'         => $lat,
            'driver_current_lng'         => $lng,
            'distance_remaining_km'      => round($distanceKm, 1),
            'driver_location_updated_at' => now(),
        ];

        // Auto-flag near destination
        if ($distanceKm <= 2 && $this->status === 'in_transit') {
            $updates['status'] = 'near_destination';
            $this->logStatus('near_destination', "Driver is {$distanceKm}km from destination");
            app(\App\Services\SmsService::class)->sendNearDestinationAlert($this);
        }

        $this->update($updates);
    }

    /**
     * Haversine formula — distance in km between two lat/lng points.
     */
    protected function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2 +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }

    /**
     * Move delivery to the next workflow status, logging history and
     * triggering SMS + order sync.
     */
    public function advanceStatus(string $status, ?string $note = null): void
    {
        $timestampField = match ($status) {
            'collected'  => 'collected_at',
            'delivered'  => 'delivered_at',
            default      => null,
        };

        $updates = ['status' => $status];
        if ($timestampField) $updates[$timestampField] = now();

        $this->update($updates);
        $this->logStatus($status, $note);

        // Sync parent order status
        $orderStatus = match ($status) {
            'collected'  => 'dispatched',
            'in_transit' => 'in_transit',
            'delivered'  => 'delivered',
            'failed'     => null,
            default      => null,
        };

        if ($orderStatus && $this->order->canTransitionTo($orderStatus)) {
            $this->order->transitionTo($orderStatus);
        }

        // Free up the driver once delivery completes
        if (in_array($status, ['delivered', 'failed'], true)) {
            $this->driver?->update(['status' => 'available']);
        }
    }

    protected function logStatus(string $status, ?string $note = null): void
    {
        $this->statusLogs()->create([
            'status'   => $status,
            'location' => $this->driver_current_lat ? "{$this->driver_current_lat},{$this->driver_current_lng}" : null,
            'lat'      => $this->driver_current_lat,
            'lng'      => $this->driver_current_lng,
            'note'     => $note,
        ]);
    }

    /**
     * Customer-facing progress percentage for the timeline UI (0-100).
     */
    public function progressPercentage(): int
    {
        return match ($this->status) {
            'pending'          => 10,
            'assigned'         => 25,
            'collected'        => 45,
            'in_transit'       => 70,
            'near_destination' => 90,
            'delivered'        => 100,
            'failed'           => 0,
            default            => 0,
        };
    }
}
