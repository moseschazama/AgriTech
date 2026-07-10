<?php
// app/Models/ProductReview.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    protected $fillable = [
        'user_id','product_id','rating','review',
        'is_verified_purchase','is_approved',
    ];

    protected $casts = [
        'is_verified_purchase' => 'boolean',
        'is_approved'          => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::saved(fn (ProductReview $r) => $r->product?->recalculateRating());
        static::deleted(fn (ProductReview $r) => $r->product?->recalculateRating());
    }

    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
