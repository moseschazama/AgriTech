<?php
// app/Models/SmsLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SmsLog extends Model
{
    protected $fillable = [
        'sent_by','type','message','recipient_phone','recipient_id',
        'status','gateway_message_id','gateway','cost',
        'related_type','related_id','sent_at','delivered_at',
    ];

    protected $casts = [
        'cost'         => 'decimal:4',
        'sent_at'      => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function sender(): BelongsTo    { return $this->belongsTo(User::class, 'sent_by'); }
    public function recipient(): BelongsTo { return $this->belongsTo(User::class, 'recipient_id'); }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }
}
