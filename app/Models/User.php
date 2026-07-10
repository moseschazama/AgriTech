<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name','last_name','email','phone','password',
        'avatar','role','status','district','region','trading_centre','village',
        'latitude','longitude','language','sms_alerts','email_alerts',
        'dark_mode','two_factor_enabled','last_login_at','last_login_ip',
    ];

    protected $hidden = ['password','remember_token','two_factor_code'];

    protected $casts = [
        'email_verified_at'      => 'datetime',
        'phone_verified_at'      => 'datetime',
        'last_login_at'          => 'datetime',
        'two_factor_expires_at'  => 'datetime',
        'sms_alerts'             => 'boolean',
        'email_alerts'           => 'boolean',
        'dark_mode'              => 'boolean',
        'two_factor_enabled'     => 'boolean',
    ];

    // ── Accessors ────────────────────────────────────────────────────

    /** Full name */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /** Initials for avatar */
    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    /** Avatar URL or generated placeholder */
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : "https://ui-avatars.com/api/?name={$this->full_name}&background=16a34a&color=fff";
    }

    // ── Role checks ───────────────────────────────────────────────────

    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isFarmer(): bool  { return $this->role === 'farmer'; }
    public function isDriver(): bool  { return $this->role === 'driver'; }
    public function isDealer(): bool  { return $this->role === 'dealer'; }
    public function isActive(): bool  { return $this->status === 'active'; }

    // ── Relationships ─────────────────────────────────────────────────

    public function farm(): HasOne
    {
        return $this->hasOne(Farm::class);
    }

    public function farms(): HasMany
    {
        return $this->hasMany(Farm::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
                    ->withPivot('status','completed_at','certificate_number')
                    ->withTimestamps();
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function innovations(): HasMany
    {
        return $this->hasMany(Innovation::class);
    }

    public function innovationVotes(): HasMany
    {
        return $this->hasMany(InnovationVote::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class, 'recipient_id');
    }

    public function diseaseDetections(): HasMany
    {
        return $this->hasMany(DiseaseDetection::class);
    }

    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class);
    }

    public function wishlistedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'wishlists')->withTimestamps();
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFarmers($query)
    {
        return $query->where('role', 'farmer');
    }

    public function scopeByDistrict($query, string $district)
    {
        return $query->where('district', $district);
    }

    // ── Business Logic ────────────────────────────────────────────────

    /**
     * Calculate the farmer's course completion percentage overall.
     */
    public function overallCourseProgress(): float
    {
        $total = $this->enrollments()->count();
        if ($total === 0) return 0;
        $completed = $this->enrollments()->where('status', 'completed')->count();
        return round(($completed / $total) * 100, 1);
    }

    /**
     * Get total spent in marketplace.
     */
    public function totalSpent(): float
    {
        return $this->orders()
                    ->where('payment_status', 'paid')
                    ->sum('total');
    }

    /**
     * Get total earned from sales.
     */
    public function totalEarned(): float
    {
        return $this->sales()
                    ->where('payment_status', 'paid')
                    ->where('status', 'delivered')
                    ->sum('total');
    }

    /**
     * Check if user has voted on a specific innovation.
     */
    public function hasVotedFor(Innovation $innovation): bool
    {
        return $this->innovationVotes()
                    ->where('innovation_id', $innovation->id)
                    ->exists();
    }

    /**
     * Check if user has wishlisted a product.
     */
    public function hasWishlisted(Product $product): bool
    {
        return $this->wishlistedProducts()
                    ->where('product_id', $product->id)
                    ->exists();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsRead(): int
    {
        return $this->unreadNotifications()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}
