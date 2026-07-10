<?php
// app/Models/DiseaseAlert.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiseaseAlert extends Model
{
    protected $fillable = [
        'disease_id','title','description','alert_type','affected_districts',
        'affected_crops','recommended_action','source','sms_sent','sms_sent_at',
        'farmers_notified','is_active','expires_at','created_by',
    ];

    protected $casts = [
        'affected_districts' => 'array',
        'affected_crops'     => 'array',
        'sms_sent'           => 'boolean',
        'sms_sent_at'        => 'datetime',
        'is_active'          => 'boolean',
        'expires_at'         => 'datetime',
    ];

    public function disease(): BelongsTo { return $this->belongsTo(Disease::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                      ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function scopeForDistrict($query, string $district)
    {
        return $query->whereJsonContains('affected_districts', $district);
    }

    /**
     * Broadcast this alert via SMS to all farmers in affected districts.
     * Delegates the actual sending to SmsService.
     */
    public function broadcastViaSms(): int
    {
        $count = app(\App\Services\SmsService::class)->broadcastDiseaseAlert($this);

        $this->update([
            'sms_sent'         => true,
            'sms_sent_at'      => now(),
            'farmers_notified' => $count,
        ]);

        return $count;
    }
}
