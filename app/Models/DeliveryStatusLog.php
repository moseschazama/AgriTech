<?php
// app/Models/DeliveryStatusLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryStatusLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'delivery_id','status','location','lat','lng','note','updated_by','created_at',
    ];

    protected $casts = [
        'lat'        => 'decimal:7',
        'lng'        => 'decimal:7',
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn (self $log) => $log->created_at = $log->created_at ?? now());
    }

    public function delivery(): BelongsTo { return $this->belongsTo(Delivery::class); }
    public function updatedBy(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
}
