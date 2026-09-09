<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class TradingCentre extends Model
{
    protected $fillable = ['district_id', 'name', 'latitude', 'longitude'];

    protected $casts = [
        'latitude'  => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Haversine distance in km from this trading centre to a given lat/lng.
     */
    public function distanceTo(float $lat, float $lng): float
    {
        if ($this->latitude === null || $this->longitude === null) {
            return PHP_FLOAT_MAX;
        }

        $earthRadiusKm = 6371;
        $dLat = deg2rad($lat - (float) $this->latitude);
        $dLng = deg2rad($lng - (float) $this->longitude);

        $a = sin($dLat / 2) ** 2 +
             cos(deg2rad((float) $this->latitude)) * cos(deg2rad($lat)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }

    /**
     * Find the nearest trading centre to a given lat/lng (optionally within a district).
     */
    public static function findNearest(float $lat, float $lng, ?string $district = null): ?self
    {
        $query = static::whereNotNull('latitude')->whereNotNull('longitude');

        if ($district) {
            $query->whereHas('district', fn ($q) => $q->where('name', $district));
        }

        $centres = $query->with('district')->get();

        if ($centres->isEmpty()) {
            return null;
        }

        return $centres->sortBy(fn ($tc) => $tc->distanceTo($lat, $lng))->first();
    }

    /**
     * Get all trading centres along a route between two points within a corridor width (km).
     */
    public static function findAlongRoute(float $lat1, float $lng1, float $lat2, float $lng2, float $corridorKm = 30): Collection
    {
        return static::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('district')
            ->get()
            ->filter(fn ($tc) => $tc->distanceToLineSegment($lat1, $lng1, $lat2, $lng2) <= $corridorKm)
            ->sortBy(fn ($tc) => $tc->distanceTo($lat1, $lng1))
            ->values();
    }

    /**
     * Perpendicular distance from a point to a line segment (simplified as distance to the line's midpoint region).
     * Uses a bounding-box pre-filter + haversine for accuracy.
     */
    protected function distanceToLineSegment(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $lat = (float) $this->latitude;
        $lng = (float) $this->longitude;

        $d1 = $this->distanceTo($lat1, $lng1);
        $d2 = $this->distanceTo($lat2, $lng2);

        $lineLength = self::haversine($lat1, $lng1, $lat2, $lng2);

        if ($lineLength == 0) return $d1;

        $t = max(0, min(1, (($lat - $lat1) * ($lat2 - $lat1) + ($lng - $lng1) * ($lng2 - $lng1)) / ($lineLength * $lineLength)));
        $projLat = $lat1 + $t * ($lat2 - $lat1);
        $projLng = $lng1 + $t * ($lng2 - $lng1);

        return self::haversine($lat, $lng, $projLat, $projLng);
    }

    protected static function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadiusKm * $c;
    }
}
