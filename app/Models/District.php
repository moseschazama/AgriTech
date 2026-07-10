<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $fillable = ['name', 'region'];

    public function tradingCentres(): HasMany
    {
        return $this->hasMany(TradingCentre::class);
    }

    public function scopeByRegion($query, string $region)
    {
        return $query->where('region', $region);
    }
}
