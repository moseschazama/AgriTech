<?php
// app/Models/FarmSale.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmSale extends Model
{
    protected $fillable = [
        'season_id','crop','quantity_kg','price_per_kg','total_revenue',
        'currency','buyer_name','buyer_type','market','sold_at','notes',
    ];

    protected $casts = [
        'quantity_kg'    => 'decimal:2',
        'price_per_kg'   => 'decimal:2',
        'total_revenue'  => 'decimal:2',
        'sold_at'        => 'date',
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(FarmSeason::class, 'season_id');
    }

    public static function buyerTypes(): array
    {
        return [
            'market'     => 'Open Market',
            'processor'  => 'Agri-Processor',
            'retailer'   => 'Retailer/Shop',
            'exporter'   => 'Exporter',
            'individual' => 'Individual Buyer',
            'cooperative'=> 'Cooperative',
        ];
    }
}
