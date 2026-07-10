<?php
// app/Models/SmsCampaign.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class SmsCampaign extends Model
{
    protected $fillable = [
        'created_by','name','message','type','target_districts','target_roles',
        'target_crop','status','scheduled_for','sent_at','total_recipients',
        'total_sent','total_delivered','total_failed','total_cost',
    ];

    protected $casts = [
        'target_districts' => 'array',
        'target_roles'     => 'array',
        'scheduled_for'    => 'datetime',
        'sent_at'          => 'datetime',
        'total_cost'       => 'decimal:4',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Resolve the target audience query based on campaign filters.
     */
    public function targetAudience(): Builder
    {
        $query = User::query()->active()->where('sms_alerts', true);

        if ($this->target_districts) {
            $query->whereIn('district', $this->target_districts);
        }

        if ($this->target_roles) {
            $query->whereIn('role', $this->target_roles);
        } else {
            $query->where('role', 'farmer'); // default: farmers only
        }

        return $query->whereNotNull('phone');
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDue($query)
    {
        return $query->where('status', 'scheduled')
                      ->where('scheduled_for', '<=', now());
    }
}
