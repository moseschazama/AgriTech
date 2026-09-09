<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingSticker extends Model
{
    protected $fillable = [
        'order_id',
        'delivery_id',
        'sticker_code',
        'qr_data',
        'generated_at',
        'scanned_at',
        'scan_location',
        'scan_lat',
        'scan_lng',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'scanned_at' => 'datetime',
        'scan_lat' => 'decimal:7',
        'scan_lng' => 'decimal:7',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public static function generateFor(Delivery $delivery): self
    {
        $date = now()->format('Ymd');
        $seq = strtoupper(substr(md5(uniqid()), 0, 6));

        $sticker = new static;
        $sticker->order_id = $delivery->order_id;
        $sticker->delivery_id = $delivery->id;
        $sticker->sticker_code = "STK-{$date}-{$seq}";
        $sticker->qr_data = url("/track/{$delivery->tracking_number}");
        $sticker->generated_at = now();
        $sticker->save();

        return $sticker;
    }
}
