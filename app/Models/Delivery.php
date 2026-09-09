<?php
// app/Models/Delivery.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Delivery extends Model
{
    protected $fillable = [
        'tracking_number','order_id','driver_id','status',
        'driver_name','driver_phone','driver_vehicle_plate','driver_vehicle_type',
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

    public function trackingStickers(): HasMany
    {
        return $this->hasMany(TrackingSticker::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(DeliveryStatusLog::class)->orderByDesc('created_at');
    }

    // ── GPS / Tracking Logic ──────────────────────────────────────────

    /**
     * Update the live position of the assigned driver, recompute remaining
     * distance and ETA, and fire tiered proximity alerts.
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

        // ── Multi-tier proximity alerts ──────────────────────────────
        $this->fireProximityAlerts($lat, $lng, $distanceKm);

        // Estimate arrival time (rough: 40 km/h average speed in Malawi)
        if ($distanceKm > 0) {
            $hoursRemaining = $distanceKm / 40;
            $updates['estimated_arrival_at'] = now()->addSeconds((int) ($hoursRemaining * 3600));
        }

        $this->update($updates);
    }

    /**
     * Fire tiered proximity alerts as the driver approaches the destination.
     * Tiers: 25km, 10km, 5km, 2km (near_destination), 0.5km (almost arrived).
     */
    protected function fireProximityAlerts(float $lat, float $lng, float $distanceKm): void
    {
        if ($this->status === 'delivered' || $this->status === 'failed') {
            return;
        }

        $alertedTiers = json_decode($this->proximity_alerted_tiers ?? '[]', true) ?? [];
        $tier = match (true) {
            $distanceKm <= 0.5  => 'arrived',
            $distanceKm <= 2    => 'near',
            $distanceKm <= 5    => 'approaching',
            $distanceKm <= 10   => 'close',
            $distanceKm <= 25   => 'entered_district',
            default             => null,
        };

        if ($tier && !in_array($tier, $alertedTiers)) {
            $alertedTiers[] = $tier;
            $this->update(['proximity_alerted_tiers' => json_encode($alertedTiers)]);

            // Auto-advance status at 2km
            if ($tier === 'near' && $this->status === 'in_transit') {
                $this->update(['status' => 'near_destination']);
                $this->logStatus('near_destination', "Driver is {$distanceKm}km from destination");

                if ($this->order && $this->order->status !== 'on_the_way') {
                    $this->order->update(['status' => 'on_the_way']);
                }
            }

            $this->sendProximityNotification($tier, $distanceKm, $lat, $lng);
        }
    }

    /**
     * Send buyer notification + SMS for each proximity tier.
     */
    protected function sendProximityNotification(string $tier, float $distanceKm, float $lat, float $lng): void
    {
        $buyer = $this->order?->buyer;
        if (!$buyer) return;

        $orderNum = $this->order->order_number ?? '';
        $dest = $this->destination_district;
        $town = $this->order->delivery_town ?? '';
        $destLabel = $town ? "{$town}, {$dest}" : $dest;
        $driverName = $this->driver?->user?->name ?? 'your driver';

        $messages = [
            'entered_district' => [
                'title' => "📦 Entering {$dest} — {$orderNum}",
                'message' => "Your order {$orderNum} has entered {$dest} district. Driver {$driverName} is on the way to {$destLabel}. Approximately {$distanceKm}km remaining.",
            ],
            'close' => [
                'title' => "🚚 Getting Closer — {$orderNum}",
                'message' => "Your order {$orderNum} is now {$distanceKm}km from {$destLabel}. Driver {$driverName} is making good progress!",
            ],
            'approaching' => [
                'title' => "📍 Almost There — {$orderNum}",
                'message' => "Your order {$orderNum} is just {$distanceKm}km from {$destLabel}! Please start getting ready to receive your package.",
            ],
            'near' => [
                'title' => "🎯 Arriving Now! — {$orderNum}",
                'message' => "Your order {$orderNum} is {$distanceKm}km away — arriving very soon! Please be available at {$destLabel} to receive your package.",
            ],
            'arrived' => [
                'title' => "✅ Driver Has Arrived! — {$orderNum}",
                'message' => "Your driver has arrived at {$destLabel} with order {$orderNum}! Please meet them to collect your package.",
            ],
        ];

        $msg = $messages[$tier] ?? null;
        if (!$msg) return;

        // In-app notification
        try {
            Notification::create([
                'user_id'    => $buyer->id,
                'title'      => $msg['title'],
                'message'    => $msg['message'],
                'type'       => 'order',
                'icon'       => 'fas fa-map-marker-alt',
                'icon_color' => $tier === 'arrived' ? '#16a34a' : '#6d28d9',
                'action_url' => route('delivery.track', $this->tracking_number),
            ]);
        } catch (\Throwable $e) {}

        // SMS alert
        try {
            $smsMessage = str_replace('📦 ', '', strip_tags($msg['message']))
                . " Track: " . route('delivery.track', $this->tracking_number);
            app(SmsService::class)->send(
                phone: $buyer->phone,
                message: $smsMessage,
                type: 'proximity_alert',
                recipient: $buyer,
                related: $this->order,
            );
        } catch (\Throwable $e) {}
    }

    /**
     * Get all trading centres along the delivery route between origin and destination.
     */
    public function getRouteWaypoints(): Collection
    {
        $originLat = (float) ($this->origin_lat ?? 0);
        $originLng = (float) ($this->origin_lng ?? 0);
        $destLat   = (float) ($this->destination_lat ?? 0);
        $destLng   = (float) ($this->destination_lng ?? 0);

        if ($originLat === 0 || $destLat === 0) {
            return collect();
        }

        return TradingCentre::findAlongRoute($originLat, $originLng, $destLat, $destLng, corridorKm: 35);
    }

    /**
     * Find the nearest trading centre to the driver's current position.
     */
    public function getNearestTradingCentre(): ?TradingCentre
    {
        if (!$this->driver_current_lat || !$this->driver_current_lng) {
            return null;
        }

        return TradingCentre::findNearest(
            (float) $this->driver_current_lat,
            (float) $this->driver_current_lng,
        );
    }

    /**
     * Get the nearest trading centre to the delivery destination.
     */
    public function getDestinationTradingCentre(): ?TradingCentre
    {
        if (!$this->destination_lat || !$this->destination_lng) {
            return null;
        }

        return TradingCentre::findNearest(
            (float) $this->destination_lat,
            (float) $this->destination_lng,
            $this->destination_district,
        );
    }

    /**
     * Calculate how far the driver is from the nearest trading centre.
     */
    public function distanceToNearestCentre(): ?array
    {
        if (!$this->driver_current_lat || !$this->driver_current_lng) {
            return null;
        }

        $centre = $this->getNearestTradingCentre();
        if (!$centre) return null;

        $distance = $centre->distanceTo(
            (float) $this->driver_current_lat,
            (float) $this->driver_current_lng,
        );

        return [
            'name'     => $centre->name,
            'district' => $centre->district?->name,
            'distance' => round($distance, 1),
            'lat'      => $centre->latitude,
            'lng'      => $centre->longitude,
        ];
    }

    /**
     * Haversine formula — distance in km between two lat/lng points.
     */
    public function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
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
            'in_transit' => 'on_the_way',
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

    public function logStatus(string $status, ?string $note = null): void
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

    /**
     * Get proximity status description for the customer UI.
     */
    public function getProximityLabel(): ?string
    {
        if (!$this->distance_remaining_km || $this->status === 'delivered') {
            return null;
        }

        return match (true) {
            $this->distance_remaining_km <= 0.5  => 'Driver has arrived!',
            $this->distance_remaining_km <= 2    => 'Arriving very soon',
            $this->distance_remaining_km <= 5    => 'Approaching destination',
            $this->distance_remaining_km <= 10   => 'Getting close',
            $this->distance_remaining_km <= 25   => 'In your area',
            default                              => 'On the way',
        };
    }
}
